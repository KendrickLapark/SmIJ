<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function list()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ? true : false
        ]);

        return response()->json(['message' => 'Usuario creado']);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ? true : false
        ]);

        return response()->json(['message' => 'Usuario actualizado']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }

    public function projects($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([], 404);
        }

        // Suponiendo que el modelo User tiene relación projects()
        $projects = $user->projects()->select('id', 'name')->get();

        return response()->json($projects);
    }
    
}
