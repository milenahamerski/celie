<?php
namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class AdminsController extends Controller {
    public function index(Request $request): void
    {
        $title = ''; 
        $this->render('admin/index', compact('title'), 'application');
    }
}
