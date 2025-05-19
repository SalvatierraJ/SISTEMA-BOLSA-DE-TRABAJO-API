<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Personas",
 *     description="Endpoints para gestionar personas"
 * )
 */
class PersonaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/personas",
     *     summary="Obtener todas las personas",
     *     tags={"Personas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de personas",
     *         @OA\JsonContent(
     *             @OA\Property(property="personas", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $personas = Persona::all();
        return response()->json([
            'personas' => $personas
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/personas/{id}",
     *     summary="Obtener una persona específica",
     *     tags={"Personas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la persona",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Persona encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="persona", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Persona no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada'
            ], 404);
        }
        return response()->json([
            'persona' => $persona
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/personas",
     *     summary="Crear una nueva persona",
     *     tags={"Personas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre", "Apellido1", "CI", "Correo"},
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido1", type="string", example="Pérez"),
     *             @OA\Property(property="Apellido2", type="string", example="García"),
     *             @OA\Property(property="CI", type="integer", example=12345678),
     *             @OA\Property(property="Genero", type="boolean", example=true),
     *             @OA\Property(property="Correo", type="string", format="email", example="juan@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Persona creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="persona", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'CI' => 'nullable|integer',
            'Genero' => 'nullable|int|max:1',
            'Id_Telefono' => 'required|integer|exists:telefono,Id_Telefono|unique:persona,Id_Telefono',
            'Correo' => 'nullable|email|max:100',
            'Id_Usuario' => 'required|integer|exists:usuario,Id_Usuario|unique:persona,Id_Usuario'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $persona = Persona::create([
            'Nombre' => $request->Nombre,
            'Apellido1' => $request->Apellido1,
            'Apellido2' => $request->Apellido2,
            'CI' => $request->CI,
            'Genero' => $request->Genero,
            'Id_Telefono' => $request->Id_Telefono,
            'Correo' => $request->Correo,
            'Id_Usuario' => $request->Id_Usuario
        ]);

        return response()->json([
            'persona' => $persona,
            'message' => 'Persona creada exitosamente'
        ], 201);
    }

    /**
     * Actualizar una persona existente
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'CI' => 'nullable|integer',
            'Genero' => 'nullable|string|max:1',
            'Id_Telefono' => 'required|integer|exists:telefono,Id_Telefono|unique:persona,Id_Telefono,'.$id.',Id_Persona',
            'Correo' => 'nullable|email|max:100',
            'Id_Usuario' => 'required|integer|exists:usuario,Id_Usuario|unique:persona,Id_Usuario,'.$id.',Id_Persona'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $persona->update([
            'Nombre' => $request->Nombre,
            'Apellido1' => $request->Apellido1,
            'Apellido2' => $request->Apellido2,
            'CI' => $request->CI,
            'Genero' => $request->Genero,
            'Id_Telefono' => $request->Id_Telefono,
            'Correo' => $request->Correo,
            'Id_Usuario' => $request->Id_Usuario
        ]);

        return response()->json([
            'persona' => $persona,
            'message' => 'Persona actualizada exitosamente'
        ], 200);
    }

    /**
     * Eliminar una persona
     */
    public function destroy($id)
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada'
            ], 404);
        }

        $persona->delete();
        return response()->json([
            'message' => 'Persona eliminada exitosamente'
        ], 200);
    }
}
