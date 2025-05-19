<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints para autenticación y gestión de usuarios"
 * )
 */
class authController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Registrar un nuevo usuario",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Usuario", "Rol", "Clave"},
     *             @OA\Property(property="Usuario", type="string", example="usuario123"),
     *             @OA\Property(property="Rol", type="integer", example=1),
     *             @OA\Property(property="Clave", type="string", example="password123"),
     *             @OA\Property(property="Clave_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="El usuario ya existe"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Iniciar sesión",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Usuario", "Clave"},
     *             @OA\Property(property="Usuario", type="string", example="usuario123"),
     *             @OA\Property(property="Clave", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/auth/user",
     *     summary="Obtener información del usuario autenticado",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function getUser()
    {
        $user = Auth::user()->load(['rol' , 'personas.estudiantes.carreras' , 'personas.telefonos','testimonios', 'multimedia']);
        return response()->json(compact('user'));
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Cerrar sesión",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully']);
    }
}