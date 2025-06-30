<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Lib\FlashMessage;

class ProfileController extends Controller
{
    public function show(): void
    {
        $title = 'Meu Perfil';
        $this->render('profile/show', compact('title'));
    }

    public function updateAvatar(): void
    {
        $image = $_FILES['user_avatar'];

        $success = $this->current_user->avatar()->update(
            $image,
            validations: [
                'extension' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                'size' => 2 * 1024 * 1024 // 2MB
            ]
        );

        if ($success) {
            FlashMessage::success('Foto de perfil atualizada com sucesso!');
        } else {
            $error = $this->current_user->errors('avatar');

            if (!$error) {
                $error = 'Não foi possível atualizar a foto de perfil.';
            }

            FlashMessage::danger($error);
        }

        $this->redirectTo(route('profile.show'));
    }
}
