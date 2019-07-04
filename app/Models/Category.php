<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * @package App\Models
 */
class Category extends Model
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
        'parent_id',
        'name',
        'color',
        'cover_photo',
        'is_active',
        'type'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent() 
    {
        return $this->hasOne('App\Models\Category', 'id', 'parent_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories() 
    {
        return $this->hasMany('App\Models\Category', 'parent_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('App\Models\Events');
    }
}
