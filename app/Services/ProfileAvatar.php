<?php

namespace App\Services;

use Core\Constants\Constants;
use Core\Database\ActiveRecord\Model;

class ProfileAvatar
{
    /** @var array<string, mixed> */
    private array $image;

    /** @var array<string, array<int, string>|int> */
    private array $validations;

    /** @var \App\Models\User */
    private Model $model;

    /**
     * @param \App\Models\User $model
     * @param array<string, array<int, string>|int> $validations
     */
    public function __construct(Model $model, array $validations = [])
    {
        $this->model = $model;
        $this->validations = $validations;
    }

    public function path(): string
    {
        if ($this->model->avatar_name) {
            $hash = md5_file($this->getAbsoluteSavedFilePath());
            return $this->baseDir() . $this->model->avatar_name . '?' . $hash;
        }

        return "/assets/images/defaults/avatar.png";
    }

    /**
     * @param array<string, mixed> $image
     * @param array<string, array<int, string>|int> $validations
     */
    public function update(array $image, array $validations = []): bool
    {
        $this->image = $image;
        if (!empty($validations)) {
            $this->validations = $validations;
        }

        if (!$this->isValidImage()) {
            return false;
        }

        if ($this->updateFile()) {
            $this->model->update([
                'avatar_name' => $this->getFileName(),
            ]);
            return true;
        }

        return false;
    }

    protected function updateFile(): bool
    {
        if (empty($this->getTmpFilePath())) {
            return false;
        }

        $this->removeOldImage();

        $resp = move_uploaded_file(
            $this->getTmpFilePath(),
            $this->getAbsoluteDestinationPath()
        );

        if (!$resp) {
            $error = error_get_last();
            throw new \RuntimeException(
                'Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error')
            );
        }

        return true;
    }

    private function getTmpFilePath(): string
    {
        return $this->image['tmp_name'];
    }

    private function removeOldImage(): void
    {
        if ($this->model->avatar_name) {
            $oldPath = $this->getAbsoluteSavedFilePath();
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    }

    private function getFileName(): string
    {
        $file_name_splitted = explode('.', $this->image['name']);
        $file_extension = strtolower(end($file_name_splitted));
        return 'avatar.' . $file_extension;
    }

    private function getAbsoluteDestinationPath(): string
    {
        return $this->storeDir() . $this->getFileName();
    }

    private function baseDir(): string
    {
        return "/assets/uploads/{$this->model::table()}/{$this->model->id}/";
    }

    private function storeDir(): string
    {
        $path = Constants::rootPath()->join('public' . $this->baseDir());

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }

    private function getAbsoluteSavedFilePath(): string
    {
        return Constants::rootPath()->join('public' . $this->baseDir())->join($this->model->avatar_name);
    }

    private function isValidImage(): bool
    {
        $this->validateMimeType();

        if (isset($this->validations['extension'])) {
            $this->validateImageExtension();
        }

        if (isset($this->validations['size'])) {
            $this->validateImageSize();
        }

        return $this->model->errors('avatar') === null;
    }

    private function validateMimeType(): void
    {
        $tmpPath = $this->getTmpFilePath();

        if (!file_exists($tmpPath) || !@getimagesize($tmpPath)) {
            $this->model->addError('avatar', 'O arquivo enviado não é uma imagem válida.');
        }
    }

    private function validateImageExtension(): void
    {
        $file_name_splitted = explode('.', $this->image['name']);
        $file_extension = strtolower(end($file_name_splitted));

        if (!in_array($file_extension, $this->validations['extension'])) {
            $this->model->addError('avatar', 'Extensão de arquivo inválida');
        }
    }

    private function validateImageSize(): void
    {
        if ($this->image['size'] > $this->validations['size']) {
            $this->model->addError('avatar', 'Tamanho do arquivo inválido');
        }
    }
}
