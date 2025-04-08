<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesion
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('idUsuario')) {
            return redirect()->route('login')->withErrors([
                'credenciales' => 'Debes iniciar sesión primero.'
            ]);
        }

        return $next($request);
    }
}
