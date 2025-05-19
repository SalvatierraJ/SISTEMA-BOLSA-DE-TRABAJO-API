<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Endpoints para gestionar roles de usuario"
 * )
 */
class RolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Obtener todos los roles",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="roles", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        $roles = Rol::all();
        return response()->json([
            'roles' => $roles
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Obtener un rol específico",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="rol", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
    public function getRollWith($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }
        return response()->json([
            'rol' => $rol
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Crear un nuevo rol",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="rol", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol creado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function createRole(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $rol = Rol::create([
            'Nombre' => $request->Nombre,
        ]);

        return response()->json([
            'rol' => $rol,
            'message' => 'Rol creado exitosamente'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Actualizar un rol existente",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="rol", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol actualizado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function updateRole(Request $request, $id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $rol->update([
            'Nombre' => $request->Nombre,
        ]);

        return response()->json([
            'rol' => $rol,
            'message' => 'Rol actualizado exitosamente'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Eliminar un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
    public function deleteRole($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $rol->delete();
        return response()->json([
            'message' => 'Rol eliminado exitosamente'
        ], 200);
    }
}
