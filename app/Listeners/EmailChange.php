<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/27/18
 * Time: 10:11 PM
 */

namespace App\Listeners;


use App\Interfaces\INotificationTypes;
use App\Components\NotificationSender;
use App\Classes\NotificationSenderEmail;
use App\Models\EmailChange as EmailChangeModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Class EmailChange
 * @package App\Listeners
 */
class EmailChange
{
    /**
     * @param array $data
     * @return string
     * @throws \ReflectionException
     */
    public function handle(array $data)
    {
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email has invalid format: ' . $data['email']);
            }

            /** @var string $hash */
            $hash = md5(config('app.salt') . $data['email'] . Auth::user()->id);

            /** @var \App\Models\EmailChange $emailChange */
            $emailChange = EmailChangeModel::firstOrCreate([
                'hash' => $hash
            ], [
                'user_id' => Auth::user()->id,
                'email' => $data['email'],
                'hash' => $hash,
            ]);

            /** @var NotificationSenderEmail $sender */
            $sender = NotificationSender::getInstance(INotificationTypes::EMAIL);

            /** Send confirmation email */
            $sender
                ->setAddressee([
                    'email' => $emailChange->email,
                    'name' => $emailChange->user->getFullName(),
                ])
                ->setTitle('C2Go::Email change confirmation')
                ->setMessage([
                    'template' => 'emails.confirmation.change',
                    'data' => [
                        'user' => $emailChange->user,
                        'email' => $emailChange->email,
                        'hash' => $emailChange->hash,
                    ]
                ])
                ->send();

            return [
                'message' => 'Confirmation email sent',
            ];
        } else if (!empty($data['hash'])) {
            /** @var EmailChangeModel|null $emailChange */
            $emailChange = EmailChangeModel::where('hash', $data['hash'])->first();

            if ($emailChange === null) {
                throw new Exception('Email change request not found');
            }

            /** Update user email */
            User::where([
                'is_blocked' => 0,
                'id' => $emailChange->user_id,
            ])
                ->first()
                ->update([
                    'email' => $emailChange->email,
                ]);

            /** Delete email change request */
            $emailChange->delete();

            return [
                'email' => $emailChange->email,
                'message' => 'Email change confirmed',
            ];
        } else {
            throw new Exception('Wrong email change data');
        }
    }
}