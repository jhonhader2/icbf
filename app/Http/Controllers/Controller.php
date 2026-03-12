<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Regional del usuario logueado. Null = puede ver todas las regionales (ej. admin).
     */
    protected function userRegionalId(): ?int
    {
        $id = Auth::user()?->regional_id;

        return $id !== null ? (int) $id : null;
    }
}
