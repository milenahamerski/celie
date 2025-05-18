<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;

class MemberController extends Controller
{
    protected string $layout = 'application';

    public function index(): void
    {
        $this->render('member/index', ['title' => 'Área do Membro']);
    }
}
