<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 8/22/18
 * Time: 10:24 PM
 */

namespace App\Classes;


use App\Interfaces\INotificationTypes;
use Exception;
use Mail;

/**
 * Class NotificationSenderEmail
 * @package App\Classes
 */
class NotificationSenderEmail extends NotificationSenderBase
{
    /**
     * NotificationSenderEmail constructor.
     */
    public function __construct()
    {
        $this->notificationType = INotificationTypes::EMAIL;
    }


    /**
     * @inheritdoc
     */
    public function send()
    {
        /** @var array $addressee */
        $addressee = $this->getAddressee();

        /** @var mixed $message */
        $message = $this->getMessage();

        if (!is_array($addressee) || !filter_var($addressee['email'], FILTER_VALIDATE_EMAIL) || empty($addressee['name'])) {
            throw new Exception(sprintf('Wrong addressee %s', json_encode($addressee)));
        }

        /** Custom html email */
        if (is_array($message)) {
            Mail::send($message['template'], $message['data'], function($message) use ($addressee) {
                $message
                    ->from(env('MAIL_FROM'), 'C2Go')
                    ->to($addressee['email'], $addressee['name'])
                    ->subject($this->getTitle());
            });
        /** Raw email */
        } else {
            Mail::raw($this->getMessage(), function($message) use ($addressee) {
                $message
                    ->from(env('MAIL_FROM'), 'C2Go')
                    ->to($addressee['email'], $addressee['name'])
                    ->subject($this->getTitle());
            });
        }
    }
}