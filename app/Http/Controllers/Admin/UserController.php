<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->with('roles')
            ->where('estatus', 'activo')
            ->orderBy('name')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(fn ($query) => $query->where('estatus', '!=', 'inactivo')),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'estatus' => 'activo',
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario guardado correctamente.');
    }

    public function edit(User $user): Response
    {
        $user->load('roles');

        return Inertia::render('Admin/Users/Form', [
            'user' => $user,
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id)
                    ->where(fn ($query) => $query->where('estatus', '!=', 'inactivo')),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->update([
            'estatus' => 'inactivo',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
