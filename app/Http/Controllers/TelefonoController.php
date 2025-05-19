<?php

namespace App\Http\Controllers;

use App\Models\Telefono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Teléfonos",
 *     description="Endpoints para gestionar teléfonos"
 * )
 */
class TelefonoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/telefonos",
     *     summary="Obtener todos los teléfonos",
     *     tags={"Teléfonos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de teléfonos",
     *         @OA\JsonContent(
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $telefonos = Telefono::all();
        return response()->json([
            'telefonos' => $telefonos
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/telefonos/{id}",
     *     summary="Obtener un teléfono específico",
     *     tags={"Teléfonos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del teléfono",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teléfono encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="telefono", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Teléfono no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }
        return response()->json([
            'telefono' => $telefono
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/telefonos",
     *     summary="Crear un nuevo teléfono",
     *     tags={"Teléfonos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Numero"},
     *             @OA\Property(property="Numero", type="integer", example=76543210)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Teléfono creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="telefono", type="object"),
     *             @OA\Property(property="message", type="string", example="Teléfono creado exitosamente")
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
            'Numero' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $telefono = Telefono::create([
            'Numero' => $request->Numero
        ]);

        return response()->json([
            'telefono' => $telefono,
            'message' => 'Teléfono creado exitosamente'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/telefonos/{id}",
     *     summary="Actualizar un teléfono existente",
     *     tags={"Teléfonos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del teléfono",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Numero"},
     *             @OA\Property(property="Numero", type="integer", example=76543210)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teléfono actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="telefono", type="object"),
     *             @OA\Property(property="message", type="string", example="Teléfono actualizado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Teléfono no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Numero' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $telefono->update([
            'Numero' => $request->Numero
        ]);

        return response()->json([
            'telefono' => $telefono,
            'message' => 'Teléfono actualizado exitosamente'
        ], 200);
    }

    /**
     * Eliminar un teléfono
     */
    public function destroy($id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }

        $telefono->delete();
        return response()->json([
            'message' => 'Teléfono eliminado exitosamente'
        ], 200);
    }
}
