<?php

namespace Tests\Acceptance\Contacts;

use App\Models\User;
use App\Models\Contact;
use Core\Database\Database;
use Core\Env\EnvLoader;
use Tests\Support\AcceptanceTester;
use Tests\Acceptance\BaseAcceptanceCest;

class ContactsCest extends BaseAcceptanceCest
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

    public function listContacts(AcceptanceTester $I): void
    {
        $I->amOnPage('/contacts');
        $I->see('Contatos');
        $I->see('Nenhum contato encontrado.');
    }

    public function createContact(AcceptanceTester $I): void
    {
        $I->amOnPage('/contacts/new');
        $I->fillField('contact[name]', 'Contato Teste');
        $I->fillField('contact[phone]', '(99) 99999-9999');
        $I->click('Enviar');
        $I->see('Contato registrado com sucesso!');
    }

    public function editContact(AcceptanceTester $I): void
    {
        $user = User::findByEmail('user@example.com');

        if (!$user) {
            $user = new User();
            $user->name = 'Usuário Teste';
            $user->email = 'user@example.com';
            $user->password = '123456';
            $user->role = 'member';
            $user->save();
        }

        $contact = new Contact();
        $contact->name = 'Contato Antigo';
        $contact->phone = '(88) 88888-8888';
        $contact->save();

        $user->contacts()->attach($contact->id, ['name' => $contact->name]);

        $I->amOnPage('/login');
        $I->fillField('user[email]', 'user@example.com');
        $I->fillField('user[password]', '123456');
        $I->click('Login');
        $I->see('Login realizado com sucesso!');

        $I->amOnPage('/contacts');
    }

    public function deleteContact(AcceptanceTester $I): void
    {
        $contact = new Contact([
            'name' => 'Contato para Excluir',
            'phone' => '(77) 77777-7777',
        ]);
        $contact->save();

        $contact->users()->attach($this->user->id, ['name' => $contact->name]);

        $I->amOnPage('/contacts');
        $I->see('Excluir');

        $I->executeJS("window.confirm = function() { return true; };");

        $I->click('Excluir');

        $I->see('Contato removido com sucesso!');
        $I->dontSee('Contato para Excluir', 'table');
    }
}
