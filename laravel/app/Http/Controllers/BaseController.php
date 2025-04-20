<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

class BaseController extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function authorizeRole(array $allowedRoles): void
    {
        $user = auth()->user();

        if (!$user || !is_array($user->roles)) {
            abort(403, 'Access denied (no roles)');
        }

        $hasAccess = collect($user->roles)->intersect($allowedRoles)->isNotEmpty();

        abort_unless($hasAccess, 403, 'Access denied (role mismatch)');
    }

}
