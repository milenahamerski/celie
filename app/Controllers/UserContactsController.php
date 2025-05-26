<?php
namespace App\Controllers;

use App\Models\UserContacts;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class UserContactsController extends Controller
{
    public function attach(Request $request): void
    {
        $data = $request->getParams();

        $userContact = new UserContacts([
            'user_id' => $this->current_user->id,
            'contact_id' => $data['contact_id']
        ]);

        if ($userContact->save()) {
            FlashMessage::success('Contato adicionado à sua lista!');
        } else {
            FlashMessage::danger('Não foi possível adicionar esse contato. Talvez ele já esteja na sua lista?');
        }

        $this->redirectTo(route('contacts.index'));
    }

    public function detach(array $params): void
    {
        $userContact = UserContacts::findBy([
            'user_id' => $this->current_user->id,
            'contact_id' => $params['contact_id']
        ]);

        if ($userContact) {
            $userContact->destroy();
            FlashMessage::success('Contato removido da sua lista!');
        } else {
            FlashMessage::danger('Contato não encontrado na sua lista.');
        }

        $this->redirectTo(route('contacts.index'));
    }
}
