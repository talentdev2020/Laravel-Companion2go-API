<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 8/22/18
 * Time: 10:24 PM
 */

namespace App\Classes;


use App\Interfaces\INotificationTypes;

/**
 * Class NotificationSenderBase
 * @package App\Classes
 */
abstract class NotificationSenderBase
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var mixed
     */
    private $message = '';

    /**
     * @var mixed
     */
    private $addressee = '';

    /**
     * @var null|int|INotificationTypes
     */
    protected $notificationType = null;

    /**
     * @param string $title
     * @return static
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @param mixed $message
     * @return static
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * @param mixed $addressee
     * @return static
     */
    public function setAddressee($addressee)
    {
        $this->addressee = $addressee;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getAddressee()
    {
        return $this->addressee;
    }


    /**
     * @return int|INotificationTypes
     */
    protected function getNotificationType(): int
    {
        return $this->notificationType;
    }


    /**
     * Send notification
     */
    abstract public function send();
}