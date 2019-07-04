<?php

namespace App\Interfaces;


/**
 * Interface IUserSettings
 * @package App\Interfaces
 */
interface IUserSettings
{
    /** @var string */
    const PROFILE_REGISTRATION_PROGRESS = 'profile_registration_progress';

    /** @var string */
    const PROFILE_PHOTO = 'profile_photo';

    /** @var string */
    const PROFILE_BIRTH_DATE = 'profile_birth_date';

    /** @var string */
    const PROFILE_IS_SUBSCRIBED = 'profile_is_subscribed';

    /** @var string */
    const PROFILE_HOME_ADDRESS_LAT_LNG = 'profile_home_address_lat_lng';

    /** @var string */
    const PROFILE_HOME_ADDRESS_FRIENDLY = 'profile_home_address_friendly';

    /** @var string */
    const PROFILE_HOME_POSTCODE = 'profile_home_postcode';

    /** @var array */
    const PROFILE_INTERESTS = 'profile_interests';

    /** @var string */
    const PROFILE_TYPE = 'profile_type';

    /** @var string */
    const PROFILE_PHONE = 'phone';

    /** @var string */
    const PROFILE_SETTINGS_GENERAL = 'profile_settings';

    /** @var string */
    const PROFILE_DISABILITY_INFORMATION = 'disability_information';

    /** @var string */
    const PROFILE_REQUIRE_ASSISTANCE = 'require_assistance';

    /** @var string */
    const NOTIFY_PARTICIPANTS_EMAIL = 'notify_participants_email';

    /** @var string */
    const NOTIFY_PARTICIPANTS_SMS = 'notify_participants_sms';

    /** @var string */
    const NOTIFY_PARTICIPANTS_PUSH = 'notify_participants_push';

    /** @var string */
    const NOTIFY_PLATFORM_EMAIL = 'notify_platform_email';

    /** @var string */
    const NOTIFY_REMINDER_EMAIL = 'notify_reminder_email';

    /** @var string */
    const NOTIFY_REMINDER_SMS = 'notify_reminder_sms';

    /** @var string */
    const FCM_PUSH_NOTIFICATION_TOKEN = 'fcm_push_notification_token';

    /** @var string */
    const FACEBOOK_NETWORK_ID = 'facebook_network_id';
}


