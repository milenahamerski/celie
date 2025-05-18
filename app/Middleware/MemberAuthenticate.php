<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class MemberAuthenticate implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check()) {
            FlashMessage::danger('Você deve estar logado para acessar essa página');
            $this->redirectTo(route('users.login'));
        }

        if (!Auth::user()->isMember()) {
            FlashMessage::danger('Você não tem permissão para acessar essa página');
            $this->redirectTo(route('admin.index'));
        }
    }

    private function redirectTo(string $location): void
    {
        header("Location: {$location}");
        exit;
    }
}
