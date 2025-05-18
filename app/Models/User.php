<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use App\Models\Admin;
use App\Models\Member;

/**
 * @property int $id
 * @property string $cpf
 * @property string $hash_password
 * @property string|null $full_name
 * @property string|null $email
 * @property string|null $phone
 * @property bool $status
 * @property string $created
 */
class User extends Model
{
    protected static string $table = 'users';

    protected static array $columns = [
        'cpf',
        'hash_password',
        'full_name',
        'email',
        'phone',
        'status',
        'created'
    ];

    protected array $errors = [];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function validates(): void
    {
        Validations::notEmpty('cpf', $this);
        Validations::uniqueness('cpf', $this);
        Validations::notEmpty('password', $this);
        Validations::notEmpty('email', $this);
    }

    public function isAdmin(): bool
    {
        return Admin::findBy(['user_id' => $this->id]) !== null;
    }

    public function isMember(): bool
    {
        return Member::findBy(['user_id' => $this->id]) !== null;
    }

    public function admin(): ?Admin
    {
        return Admin::findBy(['user_id' => $this->id]);
    }

    public function member(): ?Member
    {
        return Member::findBy(['user_id' => $this->id]);
    }

    public function authenticate(string $password): bool
    {
        if ($this->hash_password === null) {
            return false;
        }
        return password_verify($password, $this->hash_password);
    }

    public static function findByCpf(string $cpf): ?User
    {
        return self::findBy(['cpf' => $cpf]);
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute] = "{$attribute} {$message}";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __set(string $property, $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'password' &&
            $this->newRecord() &&
            !empty($value)
        ) {
            $this->hash_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }
}
