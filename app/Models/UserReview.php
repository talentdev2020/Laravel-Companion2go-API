<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class UserReview
 * @package App\Models
 */
class UserReview extends Model
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
        'user_about_id',
        'user_id',
        'event_id',
        'mark',
        'message',
        'is_active',
    ];

    /**
     * Get user about review was written
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAbout() 
    {
        return $this->belongsTo('App\Models\User', 'user_about_id', 'id');
    }


    /**
     * Get reviewer user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewer()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


    /**
     * Get event of review
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id', 'id');
    }
}
