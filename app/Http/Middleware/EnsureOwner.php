<?php

namespace App\Http\Middleware;

use App\Enums\RoleUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureOwner — memastikan user yang mengakses adalah Owner.
 * Jika bukan Owner, kembalikan 403 Forbidden.
 */
class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== RoleUser::Owner) {
            abort(403, 'Akses hanya untuk Owner.');
        }

        return $next($request);
    }
}
