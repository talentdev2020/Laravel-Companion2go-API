<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 8/22/18
 * Time: 10:31 PM
 */

namespace App\Factories;


use App\Classes\{
    NotificationSenderEmail, NotificationSenderSms, NotificationSenderPush
};
use App\Interfaces\INotificationTypes;
use App\Traits\ReflectionTrait;

/**
 * Class NotificationSenderFactory
 * @package App\Factories
 */
class NotificationSenderFactory implements INotificationTypes
{
    use ReflectionTrait;

    /**
     * @var array
     */
    private static $senders = [
        INotificationTypes::EMAIL => NotificationSenderEmail::class,
        INotificationTypes::SMS => NotificationSenderSms::class,
        INotificationTypes::PUSH => NotificationSenderPush::class,
    ];

    /**
     * @param int|INotificationTypes $notificationType
     * @return NotificationSenderEmail
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function make(int $notificationType)
    {
        /** @var int|INotificationTypes $possibleTypes */
        $possibleTypes = static::getConstants();

        if (!in_array($notificationType, $possibleTypes)) {
            throw new \Exception(sprintf('Wrong notification type (%s)', $notificationType));
        }

        return new static::$senders[$notificationType];
    }
}