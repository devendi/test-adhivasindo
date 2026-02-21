<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::query()->select('id','name','email','created_at','updated_at')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user->only('id','name','email','created_at','updated_at'), 201);
    }

    public function show(User $user)
    {
        return $user->only('id','name','email','created_at','updated_at');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255',"unique:users,email,{$user->id}"],
            'password' => ['sometimes','string','min:6'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->only('id','name','email','created_at','updated_at');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'deleted']);
    }
}