<?php
namespace App\Controllers;

use App\Models\Contact;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class ContactsController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = Contact::paginate(
            page: $request->getParam('page', 1),
            route: 'contacts.index'
        );

        $this->render('contacts/index', compact('paginator'));
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
            $this->current_user->contacts()->attach($contact->id);

            FlashMessage::success('Contato registrado com sucesso!');
            $this->redirectTo(route('contacts.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = 'Novo Contato';
            $this->render('contacts/new', compact('contact', 'title'));
        }
    }
}
