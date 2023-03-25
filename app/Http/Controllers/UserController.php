<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|string|min:3|max:255',
                'type' => 'required|string|in:admin,client'
            ]);
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->type = $validatedData['type'];
            $user->save();
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error en los datos enviados', 'errors' => $e->errors()], 422);
        }

        // Crea el usuario en la base de datos

        return response()->json(['message' => 'Usuario creado con éxito'], 201);
    }



}