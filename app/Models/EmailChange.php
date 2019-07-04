<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailChange
 * @package App\Models
 */
class EmailChange extends Model
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
        'user_id',
        'email',
        'hash',
    ];

    /**
     * @var string
     */
    protected $table = 'email_change';


    /**
     * Get user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
