<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware guard untuk memastikan hanya user dengan role 'owner' yang bisa akses.
 *
 * Saat ini semua user adalah 'owner' (single-role).
 * Middleware ini disiapkan untuk scale-up ke multi-user (owner + staff)
 * tanpa perlu mengubah logika route.
 *
 * Cara aktifkan untuk staff: tambahkan role 'staff' ke array $allowedRoles.
 */
class EnsureOwnerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'owner') {
            // Jika nanti ada staff dengan akses terbatas, redirect ke halaman yang sesuai
            abort(403, 'Akses ditolak. Halaman ini hanya untuk pemilik agen.');
        }

        return $next($request);
    }
}
