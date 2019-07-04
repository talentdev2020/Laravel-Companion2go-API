<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App\Models
 *
 * @property string $value
 * @property integer $user_id
 */
class Message extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [];

    /**
     * Attributes that should be mass-assignable.
     * @var array
     */
    protected $fillable = [
        'sender_id', 'value', 'dialog_id'
    ];

    public function sender() {
        return $this->belongsTo('App\Models\User', 'sender_id');
    }

    public function dialog() {
        return $this->belongsTo('App\Models\Dialog', 'dialog_id');
    }
}
