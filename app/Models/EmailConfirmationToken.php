<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EmailConfirmationToken
 * @package App
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property BelongsTo $user
 */
class EmailConfirmationToken extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'token'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
