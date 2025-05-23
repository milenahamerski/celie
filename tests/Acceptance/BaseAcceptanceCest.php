<?php

namespace Tests\Acceptance;

use App\Models\User;
use Core\Database\Database;
use Core\Env\EnvLoader;
use Tests\Support\AcceptanceTester;

class BaseAcceptanceCest
{
    public function _before(AcceptanceTester $page): void
    {
        EnvLoader::init();
        Database::create();
        Database::migrate();
    }

    public function _after(AcceptanceTester $page): void
    {
        Database::drop();
    }

    public function loginSuccessfullyAsMember(AcceptanceTester $page): void
    {
        $user = new User([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'member',
        ]);
        $user->save();

        $page->amOnPage('/login');

        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', $user->password);

        $page->click('Login');

        $page->see('Login realizado com sucesso!');
        $page->seeInCurrentUrl('/member');
        $page->see('Oi membro!');
    }

    public function loginSuccessfullyAsAdmin(AcceptanceTester $page): void
    {
        $user = new User([
            'name' => 'Admin 1',
            'email' => 'admin1@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'admin',
        ]);
        $user->save();

        $page->amOnPage('/login');

        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', $user->password);

        $page->click('Login');

        $page->see('Login realizado com sucesso!');
        $page->seeInCurrentUrl('/admin');
        $page->see('Oi Admin!');
    }

    public function loginWithWrongCredentials(AcceptanceTester $page): void
    {
        $user = new User([
            'name' => 'Incorreto 1',
            'email' => 'errado@example.com',
            'password' => '865rwer',
            'password_confirmation' => '865rwer',
            'role' => 'admin',
        ]);

        $page->amOnPage('/login');

        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', $user->password);

        $page->click('Login');

        $page->see('Email e/ou senha inválidos!');
        $page->seeInCurrentUrl('/login');
    }

    public function memberTriesToAccessAdminArea(AcceptanceTester $page): void
    {
        $user = new User([
        'name' => 'User 1',
        'email' => 'user1@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'member',
        ]);
        $user->save();

        $page->amOnPage('/login');
        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', '123456');
        $page->click('Login');

        $page->amOnPage('/admin');

        $page->see('Você não tem permissão para acessar essa página');
        $page->seeInCurrentUrl('/member');
    }

    public function adminTriesToAccessMemberArea(AcceptanceTester $page): void
    {
        $user = new User([
        'name' => 'Admin 1',
        'email' => 'admin1@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'admin',
        ]);
        $user->save();

        $page->amOnPage('/login');
        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', '123456');
        $page->click('Login');

        $page->amOnPage('/member');

        $page->see('Você não tem permissão para acessar essa página');
        $page->seeInCurrentUrl('/admin');
    }
    public function logoutSuccessfullyasAdmin(AcceptanceTester $page): void
    {
        $user = new User([
        'name' => 'Admin 1',
        'email' => 'admin1@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'admin',
        ]);
        $user->save();

        $page->amOnPage('/login');
        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', '123456');
        $page->click('Login');

        $page->see('Login realizado com sucesso!');
        $page->click('Logout');
        $page->see('Logout realizado com sucesso!');
        $page->seeInCurrentUrl('/login');
    }

    public function logoutSuccessfullyasMember(AcceptanceTester $page): void
    {
        $user = new User([
        'name' => 'User 1',
        'email' => 'user1@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'member',
        ]);
        $user->save();

        $page->amOnPage('/login');
        $page->fillField('user[email]', $user->email);
        $page->fillField('user[password]', '123456');
        $page->click('Login');

        $page->see('Login realizado com sucesso!');
        $page->click('Logout');
        $page->see('Logout realizado com sucesso!');
        $page->seeInCurrentUrl('/login');
    }
}
