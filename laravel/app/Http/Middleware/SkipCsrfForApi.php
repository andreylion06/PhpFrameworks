<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class SkipCsrfForApi extends Middleware
{
    /**
     * URI, які будуть виключені з CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'clients',
        'clients/*',
        'table-reservations',
        'table-reservations/*',
        'menu-items',
        'menu-items/*',
        'orders',
        'orders/*',
        'order-items',
        'order-items/*',
        'products',
        'products/*',
        'api/*',
    ];
}
