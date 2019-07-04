<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 8/22/18
 * Time: 10:24 PM
 */

namespace App\Classes;

use App\Interfaces\IUserSettings;
use App\Models\UserSetting;


/**
 * Class NotificationSenderPush
 * @package App\Classes
 */
class NotificationSenderPush extends NotificationSenderBase
{
    /**
     * @var string
     */
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @inheritdoc
     */
    public function send()
    {
        $headers = [
            'Authorization: key=AIzaSyCugEwyecoWvpJJ6GhDwpzGKACI3r3Ci40',
            'Content-Type: application/json',
        ];

        $data = [
            'notification' => [
                'title' => $this->getTitle(),
                'body' => $this->getMessage(),
                'icon' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRZjVLtJDgMTOExfMHsTZuT4G5cAmaRT0N0vnoVbblrTTKkwSOb',
                'click_action' => 'https://google.com',
            ],
            'to' => UserSetting::get(IUserSettings::FCM_PUSH_NOTIFICATION_TOKEN, $this->getAddressee())[0]
        ];

        $curlInit = curl_init($this->fcmUrl);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit, CURLOPT_HEADER,true);
        curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInit, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curlInit);

        return $result;
    }
}