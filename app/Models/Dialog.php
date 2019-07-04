<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Dialog
 * @package App\Models
 * @property string $last_message
 * @property string $last_time
 * @property string $name
 * @property integer $request_id
 * @property boolean $deleted
 */
class Dialog extends Model
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
        'last_message', 'name', 'request_id', 'last_time'
    ];

    public function members() {
        return $this->morphToMany('App\Models\User', 'dialogs_users');
    }

    public function messages() {
        return $this->hasMany('App\Models\Message', 'dialog_id');
    }

    public function request() {
        return $this->belongsTo(EventRequest::class, 'request_id');
    }
}
