<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsToMany;

/**
 * @property int $id
 * @property int $user_id
 */
class Member extends Model
{
    protected static string $table = 'members';
    protected static array $columns = ['user_id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_contacts',
            'contact_id',
            'user_id'
        );
    }
}
