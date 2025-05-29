<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            // Lanza excepción para que se maneje en Handler.php
            throw new AuthorizationException('No autorizado');
        }

        return $next($request);
    }
}

