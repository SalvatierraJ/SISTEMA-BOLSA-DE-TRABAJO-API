<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Sectores",
 *     description="Endpoints para gestionar sectores"
 * )
 */
class sectorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sectores",
     *     summary="Obtener todos los sectores",
     *     tags={"Sectores"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sectores",
     *         @OA\JsonContent(
     *             @OA\Property(property="sectors", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getSectors()
    {
        $sectors = Sector::all();
        return response()->json([
            'sectors' => $sectors
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/sectores",
     *     summary="Crear un nuevo sector",
     *     tags={"Sectores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Tecnología")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sector creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="sector", type="object")
     *         )
     *     )
     * )
     */
    public function createSector(Request $request)
    {
        $sector = Sector::create($request->all());
        return response()->json([
            'sector' => $sector
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/sectores/{id}",
     *     summary="Actualizar un sector existente",
     *     tags={"Sectores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del sector",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre"},
     *             @OA\Property(property="Nombre", type="string", example="Tecnología")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sector actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="sector", type="object")
     *         )
     *     )
     * )
     */
    public function updateSector(Request $request, $id)
    {
        $sector = Sector::find($id);
        $sector->update($request->all());
        return response()->json([
            'sector' => $sector
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/sectores/{id}",
     *     summary="Eliminar un sector",
     *     tags={"Sectores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del sector",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sector eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sector deleted successfully")
     *         )
     *     )
     * )
     */
    public function deleteSector($id)
    {
        $sector = Sector::find($id);
        $sector->delete();
        return response()->json([
            'message' => 'Sector deleted successfully'
        ], 200);
    }
}
