<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Classes\UserSettingsBase;
use App\Exceptions\WrongSettingsException;
use App\Interfaces\IUserSettings;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserSetting
 * @package App\Models
 */
class UserSetting extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Attributes that should be mass-assignable.
     * @var array
     */
    protected $fillable = [
        'section',
        'user_id',
        'value'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    /**
     * @param int|null $user_id
     * @param string $section
     * @param string $value
     * @return mixed
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public static function apply($section, $value, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::user()->id;
        }

        if (UserSettingsBase::isSectionValid($section) === false) {
            throw new WrongSettingsException('Illegal settings section.');
        }

        return self::updateOrCreate([
            'section' => $section,
            'user_id' => $user_id,
        ], [
            'section' => $section,
            'user_id' => $user_id,
            'value' => $value
        ]);
    }


    /**
     * @param $section
     * @param null $user_id
     * @return mixed
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public static function get($section, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::user()->id;
        }

        if (UserSettingsBase::isSectionValid($section) === false) {
            throw new WrongSettingsException('Illegal settings section.');
        }

        return self::where([
            'section' => $section,
            'user_id' => $user_id,
        ])
        ->pluck('value');
    }


    /**
     * @param int|null $user_id
     * @param string $location
     * @return mixed
     */
    public static function location($location, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::user()->id;
        }

        return self::updateOrCreate([
            'section' => IUserSettings::PROFILE_HOME_LOCATION,
            'user_id' => $user_id,
        ], [
            'section' => IUserSettings::PROFILE_HOME_LOCATION,
            'user_id' => $user_id,
            'value' => json_encode($location)
        ]);
    }


    /**
     * @param int|null $user_id
     * @param string $path
     * @return mixed
     */
    public static function profilePhoto($path, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::user()->id;
        }

        return self::updateOrCreate([
            'section' => IUserSettings::PROFILE_PHOTO,
            'user_id' => $user_id 
        ], [
            'section' => IUserSettings::PROFILE_PHOTO,
            'user_id' => $user_id,
            'value' => $path
        ]);
    }
}
