<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\User;
use App\Traits\ValidatorTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait UserControllerTrait
{
    use ValidatorTrait;

    public function create(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $validatorRules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirm' => 'same:password',
        ];
        $validator = $this->validator($data, $validatorRules);
        if($validator) {
            return $validator;
        }

        $role = $this->roles->getByName($this->role);
        $user = new User();
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->save();
        $user->roles()->attach($role);

        return response()->json($user);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Request $request, string $id): JsonResponse
    {
        return response()->json([]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = $this->users->getById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => '404 Not Found'
            ], 404);
        }

        $data = $request->all();
        $validatorRules = [
            'name' => 'required',
        ];
        if(isset($data['email']) && $data['email'] != $user->email) {
            $validatorRules['email'] = 'required|email|unique:users,email';
        }
        if(isset($data['password'])) {
            $validatorRules['password'] = 'required|min:6';
            $validatorRules['password_confirm'] = 'same:password';
        }
        $validator = $this->validator($data, $validatorRules);
        if($validator) {
            return $validator;
        }

        $dataFill = collect([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
        ])->filter()->toArray();

        $user->fill($dataFill);
        $user->save();

        return response()->json($user);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->users->delete($id);
        return response()->json([
            'message' => 'deleted'
        ]);
    }
}
