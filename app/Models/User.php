<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = ['name', 'email', 'encrypted_password', 'role'];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;


    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'user_contacts', 'user_id', 'contact_id')
                ->withPivot('name');
    }

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('email', $this);
        Validations::uniqueness('email', $this);

        if ($this->newRecord()) {
            Validations::passwordConfirmation($this);
            if (!in_array($this->role, ['admin', 'member'])) {
                throw new \Exception("Role inválida, deve ser 'admin' ou 'member'.");
            }
        }
    }

    public function authenticate(string $password): bool
    {
        return $this->encrypted_password !== null && password_verify($password, $this->encrypted_password);
    }

    public static function findByEmail(string $email): ?User
    {
        return User::findBy(['email' => $email]);
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
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
