<?php

namespace Tests\Unit\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Models\UserContacts;

class ContactsControllerTest extends ControllerTestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User([
            'name' => 'Usuária de Teste',
            'email' => 'teste@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'member'
        ]);
        $this->user->save();

        $_SESSION['user']['id'] = $this->user->id;
    }

    private function createContactForUser(string $name, string $phone): Contact
    {
        $contact = new Contact([
            'name' => $name,
            'phone' => $phone
        ]);
        $contact->save();

        $userContact = new UserContacts([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'name' => $name
        ]);
        $userContact->save();

        return $contact;
    }

    public function test_list_all_contacts(): void
    {
        $contacts = [];
        $contacts[] = $this->createContactForUser('Contato 1', '41999999999');
        $contacts[] = $this->createContactForUser('Contato 2', '41988888888');

        $response = $this->get(action: 'index', controllerName: 'App\Controllers\ContactsController');

        foreach ($contacts as $contact) {
            $this->assertMatchesRegularExpression("/{$contact->name}/", $response);
            $this->assertMatchesRegularExpression("/{$contact->phone}/", $response);
        }
    }

    public function test_show_create_contact_form(): void
    {
        $response = $this->get(action: 'new', controllerName: 'App\Controllers\ContactsController');

        $this->assertMatchesRegularExpression('/<form[^>]+action="\/contacts"[^>]*>/', $response);
    }

    public function test_successfully_create_contact(): void
    {
        $params = [
            'contact' => [
                'name' => 'Fulana',
                'phone' => '41999999999',
            ]
        ];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\ContactsController',
            params: $params
        );

        $this->assertMatchesRegularExpression('/Location: \/contacts/', $response);
    }

    public function test_unsuccessfully_create_contact(): void
    {
        $params = ['contact' => ['name' => '', 'phone' => '']];

        $response = $this->post(
            action: 'create',
            controllerName: 'App\Controllers\ContactsController',
            params: $params
        );

        $this->assertMatchesRegularExpression('/(Location: \/contacts|não pode ser vazio!)/', $response);
    }

    public function test_successfully_update_contact(): void
    {
        $contact = $this->createContactForUser('Fulana', '41999999999');

        $params = [
            'id' => $contact->id,
            'contact' => [
                'name' => 'Fulana Atualizada',
                'phone' => '41911111111'
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\ContactsController',
            params: $params
        );

        $this->assertMatchesRegularExpression('/Location: \/contacts/', $response);
    }

    public function test_unsuccessfully_update_contact(): void
    {
        $contact = $this->createContactForUser('Fulana', '41999999999');

        $params = [
            'id' => $contact->id,
            'contact' => [
                'name' => '',
                'phone' => ''
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\ContactsController',
            params: $params
        );

        $this->assertMatchesRegularExpression('/(Location: \/contacts|não pode ser vazio!)/', $response);
    }
}
