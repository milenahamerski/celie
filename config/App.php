<?php

namespace Config;

use App\Middleware\Authenticate;
use App\Middleware\MemberAuthenticate;
use App\Middleware\AdminsAuthenticate;

class App
{
    public static array $middlewareAliases = [
        'auth' => Authenticate::class,
        'member' => MemberAuthenticate::class,
        'admin' => AdminsAuthenticate::class,
    ];
}
