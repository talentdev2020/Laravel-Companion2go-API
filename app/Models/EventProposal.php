<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class EventProposal
 * @package App\Models
 */
class EventProposal extends Model
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
        'event_id', 
        'user_id', 
        'price', 
        'message',
        'personal_message'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event() 
    {
        return $this->belongsTo('App\Models\Event');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
