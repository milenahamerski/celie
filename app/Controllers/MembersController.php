<?php
namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class MembersController extends Controller {
    public function index(Request $request): void
    {
        $title = 'Página de Membros';
        $this->render('member/index', compact('title'), 'application');
    }
}
