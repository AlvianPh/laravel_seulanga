<?php

namespace App\Http\Middleware;

use App\Enums\RoleUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureTenant — memastikan user yang mengakses adalah Penghuni (Tenant).
 * Jika bukan (misal: Owner/Admin tanpa konteks portal), kembalikan 403 Forbidden.
 */
class EnsureTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== RoleUser::Tenant) {
            abort(403, 'Akses hanya untuk Penghuni (Tenant).');
        }

        return $next($request);
    }
}
