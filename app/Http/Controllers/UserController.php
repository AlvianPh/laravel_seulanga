<?php

namespace App\Http\Controllers;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * UserController — manajemen akun user (hanya Owner).
 *
 * Semua method dilindungi oleh UserPolicy via authorize().
 * Route group ini juga dibungkus middleware 'owner' sebagai
 * lapisan perlindungan ganda.
 */
class UserController extends Controller
{
    use AuthorizesRequests;

    /** Daftar semua user. */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('role')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    /** Form tambah user baru. */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = RoleUser::cases();

        return view('users.create', compact('roles'));
    }

    /** Simpan user baru. */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'role'     => ['required', 'in:owner,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun user berhasil dibuat.');
    }

    /** Detail user. */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /** Form edit user. */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = RoleUser::cases();

        return view('users.edit', compact('user', 'roles'));
    }

    /** Update data user. */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'     => ['required', 'in:owner,admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Akun user berhasil diperbarui.');
    }

    /** Hapus user. */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun user berhasil dihapus.');
    }
}
