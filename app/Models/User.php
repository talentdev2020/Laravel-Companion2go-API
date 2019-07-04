<?php

namespace App\Models;


use App\Interfaces\IUserSettings;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Classes\UserSettingsBase as UserSettings;

/**
 * Class User
 * @package App\Models
 *
 * @property string $email
 * @property string $first_name
 * @property boolean $email_confirmed
 * @property boolean $deactivated
 */
class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * @var array|null
     */
    private $userSettings = null;

    /**
     * @var array
     */
    protected $appends = [
        'progress',
        'settings',
        'age',
        'birth_date',
        'rank',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return int
     */
    public function getProgressAttribute()
    {
        $settings = $this->getUserSettings();

        return $settings[IUserSettings::PROFILE_REGISTRATION_PROGRESS];
    }


    /**
     * @return int
     */
    public function getAgeAttribute() 
    {
        $settings = $this->getUserSettings();

        return Carbon::parse($settings[IUserSettings::PROFILE_BIRTH_DATE])->age;
    }


    /**
     * @return string
     */
    public function getBirthDateAttribute()
    {
        $settings = $this->getUserSettings();

        return Carbon::parse($settings[IUserSettings::PROFILE_BIRTH_DATE])->format('d.m.Y');
    }

    /**
     * @return int
     */
    public function getRankAttribute() 
    {
        return rand(1, 5);
    }


    /**
     * @param string $value
     */
    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany('App\Models\EventProposal');
    }


    /**
     * List of received reviews from another users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews() 
    {
        return $this->hasMany('App\Models\UserReview', 'user_about_id');
    }


    /**
     * @return string
     */
    public function getFullName() 
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function settings() {
        return $this->hasMany(UserSetting::class, 'user_id');
    }
    
    /**
     * Get user settings
     */
    protected function getUserSettings()
    {
        if ($this->userSettings !== null) {
            return $this->userSettings;
        }

        $settings = UserSetting::where('user_id', $this->id)
            ->get()
            ->pluck('value', 'section');

        foreach ($settings as $section => $value) {
            switch ($section) {
                case 'profile_settings':
                    $settings[$section] = [];
                    break;

                case 'profile_interests':
                    $data = json_decode($value);

                    if (!empty($data->categories)) {
                        $settings[$section] = Category::with([
                            'categories' => function($query) use ($data) {
                                $query->whereIn('id', $data->categories);
                            }
                        ])
                        ->where('is_active', 1)
                        ->whereNull('parent_id')
                        ->orderBy('order', 'ASC')
                        ->get()
                        ->toArray();
                    }
                    break;

                default:
                    $settings[$section] = json_decode($value);
                    if (in_array($settings[$section], ['', null], true)) {
                        $settings[$section] = $value;
                    }
                    break;
            }
        }

        /** Mock with required fields */
        $this->userSettings = array_merge(
            UserSettings::settingSections(),
            ['profile_interests' => []],
            $settings->toArray()
        );

        return $this->userSettings;
    }


    /**
     * Get additional model "settings" field
     * @return array
     * @throws \ReflectionException
     */
    public function getSettingsAttribute() 
    {
        return $this->getUserSettings();
    }

    
    /**
     * Get user phone
     * @return string 
     */
    public function getPhone(): string
    {
        $phone = UserSetting::where([
            'user_id' => $this->id,
            'section' => 'phone',
        ])
        ->first();
        
        /** If the user has no any saved phones */
        if ($phone === null) {
            return '';
        }
        
        return $phone->value;
    }


    /**
     * Does user has the phone number
     * @param string $phone
     * @return bool
     */
    public function isPhone(string $phone): bool
    {
        /** @var string $phone */
        $userPhone = $this->getPhone();

        if (strlen($userPhone) >= 10 
            && preg_match('/' . str_replace('+', '\+', $phone) . '$/', $userPhone)) {
            return true;
        }
        
        return false;
    }

    
    /**
     * Add phone number
     * @param string $phone
     * @return bool
     */
    public function setPhone(string $phone): bool
    {
        if ($this->isPhone($phone)) {
            return false;
        }
        
        UserSetting::updateOrCreate([
            'value' => $phone
        ], [
            'user_id' => $this->id,
            'section' => 'phone',
            'value' => $phone
        ]);
        
        return true;
    }

    
    /**
     * Get current logged in user account type
     */
    public function getAccountType()
    {
        $settings = $this->getUserSettings();

        return (int)$settings['profile_type'] ?? null;
    }
}
