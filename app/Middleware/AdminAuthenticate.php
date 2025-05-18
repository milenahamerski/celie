<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class AdminAuthenticate implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check()) {
            FlashMessage::danger('Você deve estar logado para acessar essa página');
            $this->redirectTo(route('users.login'));
        }

        if (!Auth::user()->isAdmin()) {
            FlashMessage::danger('Você não tem permissão para acessar essa página');
            $this->redirectTo(route('member.index'));
        }
    }

    private function redirectTo(string $location): void
    {
        header("Location: {$location}");
        exit;
    }
}
