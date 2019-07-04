<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRequest extends Model
{
    const STATE_NEW = 1;
    const STATE_ACCEPTED = 2;
    const STATE_REJECTED = 3;

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
        'event_proposals_id',
        'user_id',
        'message',
        'state',
        'is_active',
    ];

    /**
     * Author of the request
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    /**
     * Author of the request
     */
    public function requestor()
    {
        return $this->belongsTo('App\Models\User', 'requestor_user_id');
    }
    
    /**
     * Author of the proposal
     */
    public function proposal() 
    {
        return $this->hasOne('App\Models\EventProposal', 'id', 'event_proposals_id');
    }

    /**
     * Dialog of the proposal
     */
    public function dialog()
    {
        return $this->hasOne(Dialog::class, 'request_id');
    }
    
    /**
     * Author of the request
     */
    public function proposer() 
    {
        return $this->hasManyThrough('App\Models\User', 'App\Models\EventProposal', 'id', 'id', 'event_proposals_id', 'user_id')->limit(1);
    }
}
