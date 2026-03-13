<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function getUsers(): JsonResponse
    {
        $users = User::leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->selectRaw('
            users.*,
            roles.name role
        ')->where(['estatus' => 'activo'])->get();
        return response()->json([
            'data' => $users->load("roles"),
        ], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validate = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'email' => ['required', 'email',Rule::unique('users')->ignore('inactivo','estatus')],
            'password' => ['required',Rules\Password::min(8)->letters()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ];
        if($request->role == 'Instructor') {
            $validate = [
                'name' => ['required', 'string', 'max:255'],
                'role' => ['required', Rule::exists('roles', 'name')],
            ];
        }
        $request->validate($validate);

        $data = [
            'name' => $request->name,
        ];
        if ($request->role != 'Instructor') {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
        }

        $user = User::create($data);

        $user->assignRole($request->role);

        return response()->json([
            'data' => $user,
        ], JsonResponse::HTTP_OK);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validate =[
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'email' => ['required', 'email',Rule::unique('users')->where(function ($query) use ($user) {
                $query->whereNotIn('id', [$user->id])->whereNotIn('estatus', ['inactivo']);
                return $query;
            })],
        ];
        if($request->role == 'Instructor') {
            $validate = [
                'name' => ['required', 'string', 'max:255'],
                'role' => ['required', Rule::exists('roles', 'name')],
            ];
        }
        $request->validate($validate);
        $data = [
            'name',
        ];
        if ($request->role != 'Instructor') {
            $data = [
                'name',
                'email',
                'cliente_id',
            ];
        }
        $params=$request->only($data);
        if(isset($request->nota))
        {
            $params['nota']=$request->nota;
        }
        $user->update($params);

        if(!$user->hasRole($request->role))
        {
            $roles= $user->getRoleNames();
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
            $user->assignRole($request->role);
        }

        return response()->json([
            'data' => $user,
        ], JsonResponse::HTTP_OK);
    }

    public function updatePassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'password' => [
                'required',
                Rules\Password::min(8)->letters(),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'data' => $user,
        ], JsonResponse::HTTP_OK);
    }

    public function updatePerfil(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $request->user()->update($request->only([
            'name',
            'email',
        ]));

        return response()->json([
            'data' => $request->user(),
        ], JsonResponse::HTTP_OK);
    }

    public function getUser(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load("roles"),
        ], JsonResponse::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->update([
            'estatus' => 'inactivo',
        ]);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
