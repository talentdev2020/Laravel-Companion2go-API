<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Event
 * @package App\Models
 */
class Event extends Model
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
        'category_id', 
        'user_id', 
        'name', 
        'description', 
        'destination', 
        'destination_latlng', 
        'dispatch', 
        'dispatch_latlng', 
        'date',
        'is_top',
        'is_active',
        'location_to',
        'location_from',
        'location_to_latlng',
        'location_from_latlng'
    ];
    
    /**
     * @var array 
     */
    protected $appends = [
        'proposals_count'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() 
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bestProposal() 
    {
        return $this->hasOne('App\Models\EventProposal')->orderBy('price', 'ASC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals() 
    {
        return $this->hasMany('App\Models\EventProposal');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests() 
    {
        return $this->hasMany('App\Models\EventRequest');
    }
    
    /**
     * Get the event date.
     * @param  string  $value
     * @return string
     */
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y H:i');
    }
    
    /**
     * Get event proposals amount
     * @return int
     */
    public function getProposalsCountAttribute()
    {
        return EventProposal::where('event_id', $this->id)->count();
    }
}
