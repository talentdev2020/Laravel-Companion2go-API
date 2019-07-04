<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 8/22/18
 * Time: 11:14 PM
 */

namespace App\Components;


use App\Traits\SingletonTrait;
use App\Interfaces\INotificationTypes;
use App\Factories\NotificationSenderFactory;
use App\Classes\{
    NotificationSenderEmail, NotificationSenderSms, NotificationSenderPush
};

/**
 * Class NotificationSender
 * @package App\Components
 */
class NotificationSender
{
    use SingletonTrait;

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @param int|INotificationTypes $notificationType
     * @return NotificationSenderEmail|NotificationSenderSms|NotificationSenderPush
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function getInstance(int $notificationType)
    {
        if (!isset(static::$instances[$notificationType])) {
            static::$instances[$notificationType] = NotificationSenderFactory::make($notificationType);
        }

        return static::$instances[$notificationType];
    }
}