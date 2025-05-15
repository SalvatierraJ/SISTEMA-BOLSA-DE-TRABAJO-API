<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Persona;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthSocialController extends Controller
{
    public function socialLogin(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['error' => 'Token no enviado'], 400);
        }

        try {
            // Obtener encabezado del JWT
            $tokenParts = explode('.', $token);
            $header = json_decode(base64_decode($tokenParts[0]), true);
            $kid = $header['kid'] ?? null;

            if (!$kid) {
                return response()->json(['error' => 'Token inválido: sin "kid"'], 400);
            }

            // Obtener claves públicas de Auth0
            $jwks = json_decode(file_get_contents('https://'.env('AUTH0_DOMAIN').'/.well-known/jwks.json'), true);
            $keys = collect($jwks['keys'])->keyBy('kid');

            if (!$keys->has($kid)) {
                return response()->json(['error' => 'Clave pública no encontrada para el token'], 400);
            }

            $x5c = $keys[$kid]['x5c'][0];
            $publicKey = "-----BEGIN CERTIFICATE-----\n" . chunk_split($x5c, 64, "\n") . "-----END CERTIFICATE-----\n";

            // Decodificar el token con RS256
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            $email = $decoded->email ?? null;
            $name = $decoded->name ?? 'Sin nombre';

            if (!$email) {
                return response()->json(['error' => 'El token no contiene email.'], 400);
            }

            $usuario = Usuario::where('Usuario', $email)->first();
            if (!$usuario) {
                $rolEstudiante = Rol::where('Nombre', 'Estudiante')->first();


                if (!$rolEstudiante) {
                    return response()->json(['error' => 'Rol Estudiante no encontrado'], 500);
                }

                $usuario = Usuario::create([
                    'Usuario' => $email,
                    'Clave' => bcrypt('12345678'),
                    'Estado' => 'Activo',
                    'Id_Rol' => $rolEstudiante->Id_Rol,
                ]);
                $persona = Persona::create([
                    'Nombre' => $name,
                    'Correo' => $email,
                    'Apellido1' => $name,
                    'Id_Usuario' => $usuario->Id_Usuario,
                ]);

                $estudiante = Estudiante::create([
                    'Id_Persona' => $persona->Id_Persona,
                ]);
            }

            $tokenJWT = auth()->login($usuario);

            return response()->json([
                'token' => $tokenJWT,
                'user' => $usuario,
                'rol' => $usuario->rol->Nombre ?? 'Estudiante'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al autenticar: ' . $e->getMessage()
            ], 401);
        }
    }
}
