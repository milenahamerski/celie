<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 */
class Contact extends Model
{
    protected static string $table = 'contacts';
    protected static array $columns = ['name', 'phone'];

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
