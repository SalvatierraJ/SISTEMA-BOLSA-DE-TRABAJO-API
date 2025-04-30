<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class authController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Usuario' => 'required|min:3|max:100',
            'Rol' => 'required|integer',
            'Clave' => 'required|min:6|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $existingUser = Usuario::where('Usuario', $request->input('Usuario'))->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'User already exists'
            ], 409);
        }

        Usuario::create([
            'Usuario' => $request->Usuario,
            'Id_Rol' => $request->Rol,
            'Clave' => bcrypt($request->Clave)
        ]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Usuario' => 'required',
            'Clave' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = [
            'Usuario' => $request->Usuario,
            'password' => $request->Clave
        ];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }

            return response()->json(compact('token'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }
    }

    public function getUser()
    {
        $user = Auth::user()->load('rol');
        return response()->json(compact('user'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully']);
    }
}
