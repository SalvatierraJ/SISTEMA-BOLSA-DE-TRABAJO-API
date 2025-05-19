<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Carreras",
 *     description="Endpoints para gestionar carreras"
 * )
 */
class CarreraController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/carreras",
     *     summary="Obtener todas las carreras",
     *     tags={"Carreras"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de carreras",
     *         @OA\JsonContent(
     *             @OA\Property(property="carreras", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        $carreras = Carrera::all();
        return response()->json([
            'carreras' => $carreras
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/carreras",
     *     summary="Crear una nueva carrera",
     *     tags={"Carreras"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Ingeniería Informática"),
     *             @OA\Property(property="Descripcion", type="string", example="Carrera enfocada en el desarrollo de software")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Carrera creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrera creada exitosamente"),
     *             @OA\Property(property="carrera", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function createCarrera(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100|unique:carrera,Nombre',
            'Descripcion' => 'nullable|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $carrera = Carrera::create([
            'Nombre' => $request->Nombre,
            'Descripcion' => $request->Descripcion
        ]);

        return response()->json([
            'message' => 'Carrera creada exitosamente',
            'carrera' => $carrera
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/carreras/{id}",
     *     summary="Obtener una carrera específica",
     *     tags={"Carreras"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la carrera",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrera encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="carrera", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrera no encontrada"
     *     )
     * )
     */
    public function buscarCarreraPor($id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }
        return response()->json([
            'carrera' => $carrera
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/carreras/{id}",
     *     summary="Actualizar una carrera existente",
     *     tags={"Carreras"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la carrera",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Ingeniería Informática"),
     *             @OA\Property(property="Descripcion", type="string", example="Carrera enfocada en el desarrollo de software")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrera actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrera actualizada exitosamente"),
     *             @OA\Property(property="carrera", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrera no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function actualizarCarrera(Request $request, $id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100|unique:carrera,Nombre,'.$id.',Id_Carrera',
            'Descripcion' => 'nullable|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $carrera->update($request->all());

        return response()->json([
            'message' => 'Carrera actualizada exitosamente',
            'carrera' => $carrera
        ], 200);
    }

    public function eliminarCarrera($id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }

        $carrera->delete();

        return response()->json([
            'message' => 'Carrera eliminada exitosamente'
        ], 200);
    }
}
