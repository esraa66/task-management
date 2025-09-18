<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

trait AuthorizesRoles
{
    protected function authorizeManagerOnly(): void
    {
        if (!Auth::user()->hasRole('manager')) {
            throw ValidationException::withMessages([
                'permission' => "Only managers can perform this action."
            ]);
        }
    }
}
