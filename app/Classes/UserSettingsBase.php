<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/17/18
 * Time: 10:45 PM
 */

namespace App\Classes;


use App\Interfaces\IUserSettings;
use App\Traits\ReflectionTrait;

/**
 * Class UserSettingsBase
 * @package App\Classes
 */
abstract class UserSettingsBase implements IUserSettings
{
    use ReflectionTrait;

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function settingSections(): array
    {
        $result = [];
        foreach (static::getConstants() as $section) {
            $result[$section] = '';
        }

        return $result;
    }


    /**
     * @param string $section
     * @return bool
     * @throws \ReflectionException
     */
    public static function isSectionValid(string $section): bool
    {
        return in_array($section, static::getConstants(), true);
    }
}