<?php

namespace App;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Proposal
 * @package App
 *
 * @property integer $user_id
 * @property integer$category_id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $date
 * @property string $place
 * @property string $place_latlng
 * @property string $message
 */
class Proposal extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'proposals';

    public $timestamps = true;

    protected $fillable = [
        'title', 'description', 'url', 'date', 'place', 'place_latlng', 'message'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
