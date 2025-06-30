<?php

namespace Tests\Acceptance\Profile;

use App\Models\User;
use Core\Database\Database;
use Core\Env\EnvLoader;
use Tests\Support\AcceptanceTester;
use Tests\Acceptance\BaseAcceptanceCest;

class UploadAvatarCest extends BaseAcceptanceCest
{
    private User $user;

    public function _before(AcceptanceTester $page): void
    {
        EnvLoader::init();
        Database::create();
        Database::migrate();

        $this->user = new User([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'member',
        ]);
        $this->user->save();

        $page->amOnPage('/login');
        $page->fillField('user[email]', 'user@example.com');
        $page->fillField('user[password]', '123456');
        $page->click('Login');
        $page->see('Login realizado com sucesso!');
    }

    public function _after(AcceptanceTester $page): void
    {
        Database::drop();
    }

    public function testUploadValidImage(AcceptanceTester $page): void
    {
        $page->amOnPage('/profile');

        $page->seeElement('form');

        $page->attachFile('#image_preview_input', 'avatar_test.jpg');
        $page->click('#image_preview_submit');

        #$page->waitForElementVisible('.alert-success', 5);
        $page->see('Foto de perfil atualizada com sucesso!');
        $page->seeInCurrentUrl('/profile');
        $page->seeElement('#image_preview');
    }

    // public function testUploadInvalidFileExtension(AcceptanceTester $page): void
    // {
    //     $page->amOnPage('/profile');

    //     $page->seeElement('form');

    //     $page->attachFile('#image_preview_input', 'arquivo_invalido.ts');

    //     $page->click('#image_preview_submit');

    //     #$page->waitForElementVisible('.alert-danger', 5);
    //     $page->see('Extensão de arquivo inválida');
    //     $page->seeInCurrentUrl('/profile');
    // }
}
