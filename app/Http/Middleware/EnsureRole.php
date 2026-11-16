<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memeriksa hak akses pengguna berdasarkan role.
 *
 * Membatasi akses route agar hanya dapat
 * digunakan oleh pengguna dengan role yang diizinkan.
 */
class EnsureRole
{
    /**
     * Memvalidasi role pengguna sebelum request diteruskan.
     *
     * Contoh penggunaan pada route:
     * EnsureRole::class . ':admin,guru'
     *
     * @param Request $request Request yang sedang diproses.
     * @param Closure $next Fungsi untuk meneruskan request.
     * @param string ...$roles Daftar role yang diizinkan.
     *
     * @return Response Respons dari proses berikutnya.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * Jika pengguna tidak memiliki akses.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if(!user || ! in_array($user-role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}