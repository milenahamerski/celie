<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property int $is_admin
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = ['name', 'email', 'encrypted_password', 'is_admin'];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('email', $this);
        Validations::uniqueness('email', $this);

        if ($this->newRecord()) {
            Validations::passwordConfirmation($this);
        }
    }

    public function authenticate(string $password): bool
    {
        return $this->encrypted_password !== null && password_verify($password, $this->encrypted_password);
    }

    public static function findByEmail(string $email): User|null
    {
        return User::findBy(['email' => $email]);
    }

    public function afterSave(): void
    {
        if ($this->newRecord()) {
            if ($this->is_admin) {
                $admin = new Admin();
                $admin->user_id = $this->id;
                $admin->save();
            } else {
                $member = new Member();
                $member->user_id = $this->id;
                $member->save();
            }
        }
    }

    public function __set(string $property, mixed $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'password' &&
            $this->newRecord() &&
            $value !== null && $value !== ''
        ) {
            $this->encrypted_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === 1;
    }

    public function isMember(): bool
    {
        return $this->is_admin === 0;
    }
}
