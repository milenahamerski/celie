<?php

namespace App\Models;

use Lib\Validations;
use App\Models\User;

/**
 * @property int $id
 * @property int $user_id
 */
class Admin extends User
{
    protected static string $table = 'admins';
    protected static array $columns = ['user_id'];

    protected array $errors = [];

    public function validates(): void
    {
        Validations::notEmpty('user_id', $this);

        $existingAdmin = self::findBy(['user_id' => $this->user_id]);
        if ($existingAdmin !== null && $existingAdmin->id !== $this->id) {
            $this->addError('user_id', 'já está associado a um admin.');
        }
    }

    public function user(): ?User
    {
        return User::findById($this->user_id);
    }

    public static function findByUserId(int $userId): ?Admin
    {
        return self::findBy(['user_id' => $userId]);
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute] = "{$attribute} {$message}";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
