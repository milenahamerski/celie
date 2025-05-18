<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 */
class Admin extends Model
{
    protected static string $table = 'admins';
    protected static array $columns = ['user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
