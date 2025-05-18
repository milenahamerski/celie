<?php

namespace Config;

class App
{
    public static array $middlewareAliases = [
        'auth' => \App\Middleware\Authenticate::class,          
        'auth.admin' => \App\Middleware\AdminAuthenticate::class,
        'auth.member' => \App\Middleware\MemberAuthenticate::class,
    ];
}
