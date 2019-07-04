<?php

namespace App\Interfaces;


/**
 * Interface INotificationTypes
 * @package App\Interfaces
 */
interface INotificationTypes
{
    /** Notification via email */
    const EMAIL = 1;
    
    /** Notification via sms */
    const SMS = 2;

    /** Notification via push */
    const PUSH = 3;
}


