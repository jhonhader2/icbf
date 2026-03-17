<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * @method \Illuminate\Routing\RouteMiddleware middleware(string|array $middleware, array $options = [])
 */
abstract class Controller extends BaseController
{
    /**
     * Regional del usuario logueado. Null = puede ver todas las regionales (ej. admin).
     */
    protected function userRegionalId(): ?int
    {
        $id = Auth::user()?->regional_id;

        return $id !== null ? (int) $id : null;
    }

    /**
     * Persona vinculada al usuario autenticado por email. Null si no hay usuario o no existe persona.
     */
    protected function currentPersona(): ?Persona
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        return Persona::query()
            ->whereRaw('LOWER(email_address) = ?', [mb_strtolower($user->email)])
            ->first();
    }
}
