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

    public function _before(AcceptanceTester $I): void
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

        $I->amOnPage('/login');
        $I->fillField('user[email]', 'user@example.com');
        $I->fillField('user[password]', '123456');
        $I->click('Login');
        $I->see('Login realizado com sucesso!');
    }

    public function _after(AcceptanceTester $I): void
    {
        Database::drop();
    }

    public function testUploadValidImage(AcceptanceTester $I): void
    {
        $I->amOnPage('/profile');

        $I->seeElement('form');

        $I->attachFile('#image_preview_input', 'avatar_test.jpg');
        $I->click('#image_preview_submit');

        $I->waitForElementVisible('.alert-success', 5);
        $I->see('Foto de perfil atualizada com sucesso!', '.alert-success');
        $I->seeInCurrentUrl('/profile');
        $I->seeElement('#image_preview');
    }

    // public function testUploadInvalidFileExtension(AcceptanceTester $I): void
    // {
    //     $I->amOnPage('/profile');

    //     $I->seeElement('form');

    //     $I->attachFile('#image_preview_input', 'arquivo_invalido.ts');

    //     $I->click('#image_preview_submit');

    //     $I->waitForElementVisible('.alert-danger', 5);
    //     $I->see('Extensão de arquivo inválida', '.alert-danger');
    //     $I->seeInCurrentUrl('/profile');
    // }
}
