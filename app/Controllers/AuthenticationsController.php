<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class AuthenticationsController extends Controller
{
    public function showLogin(Request $request): void
    {
        $title = '';
        $this->render('authentications/new', compact('title'), 'authLayout');
    }

    public function authenticate(Request $request): void
    {
        $params = $request->getParam('user');
        $user = User::findBy(['cpf' => $params['cpf']]);

        if ($user && $user->authenticate($params['password'])) {
            Auth::login($user);
            FlashMessage::success('Login realizado com sucesso!');

            if ($user->isAdmin()) {
                $this->redirectTo(route('admin.index'));
            } elseif ($user->isMember()) {
                $this->redirectTo(route('member.index'));
            } else {
                FlashMessage::danger('Usuário sem perfil válido!');
                Auth::logout();
                $this->redirectTo(route('users.login'));
            }
        } else {
            FlashMessage::danger('CPF e/ou senha inválidos!');
            $this->redirectTo(route('users.login'));
        }
    }

    public function destroy(): void
    {
        Auth::logout();
        FlashMessage::success('Logout realizado com sucesso!');
        $this->redirectTo(route('users.login'));
    }
}
