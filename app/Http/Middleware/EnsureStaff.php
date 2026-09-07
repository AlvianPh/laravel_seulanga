<?php

namespace App\Http\Middleware;

use App\Enums\RoleUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureStaff — memastikan user yang mengakses adalah Owner atau Admin.
 * Jika bukan (misal: Tenant), kembalikan 403 Forbidden.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            abort(403, 'Akses hanya untuk Owner dan Admin.');
        }

        return $next($request);
    }
}
