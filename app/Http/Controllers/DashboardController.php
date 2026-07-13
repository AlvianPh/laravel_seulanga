<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DashboardController — halaman utama setelah login.
 * Dapat diakses oleh Owner maupun Admin.
 */
class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard. */
    public function index(): View
    {
        return view('dashboard');
    }
}
