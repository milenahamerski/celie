<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use PhpParser\Node\Expr\FuncCall;

/**
 *  @property int $id
 *  @property int $contact_id
 *  @property int $user_id
 *  @property string $name
 */

class UserContacts extends Model
{
    protected static string $table = 'user_contacts';
    protected static array $columns = ['contact_id', 'user_id', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function validates(): void
    {
        Validations::uniqueness(['user_id', 'contact_id'], $this);
    }
}
