<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';
}
