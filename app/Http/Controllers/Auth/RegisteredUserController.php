<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * RegisteredUserController — menangani registrasi user.
 *
 * PERHATIAN: Registrasi publik DINONAKTIFKAN di sistem ini.
 * User baru hanya bisa dibuat oleh Owner melalui menu Manajemen User
 * (UserController@store di route /users).
 *
 * Route /register tetap ada untuk kompatibilitas Breeze, tapi
 * semua request dialihkan kembali ke halaman login.
 */
class RegisteredUserController extends Controller
{
    /**
     * Redirect ke login — registrasi publik tidak diizinkan.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'Registrasi tidak tersedia. Hubungi Owner untuk mendapatkan akun.');
    }

    /**
     * Tolak request POST register — registrasi publik tidak diizinkan.
     */
    public function store(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'Registrasi tidak tersedia. Hubungi Owner untuk mendapatkan akun.');
    }
}
