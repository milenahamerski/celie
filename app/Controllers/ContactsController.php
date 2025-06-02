<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\UserContacts;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class ContactsController extends Controller
{
    public function index(Request $request): void
    {
        $contacts = $this->current_user->contacts()->withPivot('name')->get();

        $this->render('contacts/index', compact('contacts'));
    }


    public function new(): void
    {
        $contact = new Contact();
        $title = 'Novo Contato';
        $this->render('contacts/new', compact('contact', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParams();
        $contact = new Contact($params['contact']);

        if ($contact->save()) {
            $this->current_user->contacts()->attach($contact->id, [
            'name' => $contact->name
            ]);

            FlashMessage::success('Contato registrado com sucesso!');
            $this->redirectTo(route('contacts.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = 'Novo Contato';
            $this->render('contacts/new', compact('contact', 'title'));
        }
    }
    public function destroy(Request $request): void
    {
        $id = $request->getParam('id');
        $contacts = $this->current_user->contacts()->get();

        $contact = array_filter($contacts, fn($c) => $c->id == $id);
        $contact = array_values($contact)[0] ?? null;

        if (!$contact) {
            FlashMessage::danger('Contato não encontrado ou não pertence a você.');
            $this->redirectTo(route('contacts.index'));
            return;
        }

        $this->current_user->contacts()->detach($id);

        FlashMessage::success('Contato removido com sucesso!');
        $this->redirectTo(route('contacts.index'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $name = trim((string) ($request->getParam('name') ?? ''));

        if (empty($id) || empty($name)) {
            FlashMessage::danger('Dados inválidos.');
            $this->redirectTo(route('contacts.index'));
            return;
        }

        $userContact = UserContacts::findBy([
        'user_id' => $this->current_user->id,
        'contact_id' => $id,
        ]);

        if (!$userContact) {
            FlashMessage::danger('Contato não pertence a você.');
            $this->redirectTo(route('contacts.index'));
            return;
        }

        $userContact->name = $name;

        if ($userContact->save()) {
            FlashMessage::success('Nome do contato atualizado com sucesso!');
        } else {
            FlashMessage::danger('Erro ao atualizar o nome do contato.');
        }

        $this->redirectTo(route('contacts.index'));
    }
}
