<?php

namespace App\Http\Controllers;

use App\Models\Postulacion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Postulaciones",
 *     description="Endpoints para gestionar postulaciones"
 * )
 */
class applicationsController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/admin/postulaciones",
     *     operationId="getAllPostulaciones",
     *     summary="Listar todas las postulaciones",
     *     tags={"Postulaciones"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de postulaciones"
     *     )
     * )
     */

    public function allAplications()
    {
        $aplications = Postulacion::all();
        return response()->json(['aplications' => $aplications], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/postulaciones",
     *     summary="Crear una nueva postulación",
     *     operationId="createAplication",
     *     tags={"Postulaciones"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Id_Trabajo", "Id_Candidato", "Estado", "Fecha_Envio"},
     *             @OA\Property(property="Id_Trabajo", type="integer", example=1),
     *             @OA\Property(property="Id_Candidato", type="integer", example=2),
     *             @OA\Property(property="Estado", type="string", example="pendiente"),
     *             @OA\Property(property="Fecha_Envio", type="string", format="date", example="2024-05-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Postulación creada correctamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación"
     *     )
     * )
     */
    public function createAplication(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Id_Trabajo' => 'required|integer',
            'Id_Candidato' => 'required|integer',
            'Estado' => 'required|string|max:100',
            'Fecha_Envio' => 'required|date',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $aplication = Postulacion::create($request->all());
        return response()->json(['aplication' => $aplication], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/postulaciones/{id}",
     *     summary="Obtener una postulación por ID",
     *     operationId="getPostulacionById",
     *     tags={"Postulaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la postulación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Postulación encontrada"),
     *     @OA\Response(response=404, description="Postulación no encontrada")
     * )
     */
    public function getAplication($id)
    {
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json(['message' => 'No se encontró la aplicación'], 404);
        }
        return response()->json(['aplication' => $aplication], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/postulaciones/{id}",
     *     summary="Actualizar una postulación",
     *      operationId="updateAplication",
     *     tags={"Postulaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la postulación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Id_Trabajo", "Id_Candidato", "Estado", "Fecha_Envio"},
     *             @OA\Property(property="Id_Trabajo", type="integer", example=1),
     *             @OA\Property(property="Id_Candidato", type="integer", example=2),
     *             @OA\Property(property="Estado", type="string", example="aceptado"),
     *             @OA\Property(property="Fecha_Envio", type="string", format="date", example="2024-05-10")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Postulación actualizada correctamente"),
     *     @OA\Response(response=404, description="Postulación no encontrada"),
     *     @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function updateAplication(Request $request, $id)
    {
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json(['message' => 'No se encontró la aplicación'], 404);
        }
        $validate = Validator::make($request->all(), [
            'Id_Trabajo' => 'required|integer',
            'Id_Candidato' => 'required|integer',
            'Estado' => 'required|string|max:100',
            'Fecha_Envio' => 'required|date',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $aplication->update($request->all());
        return response()->json(['aplication' => $aplication], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/postulaciones/{id}",
     *     summary="Eliminar una postulación",
     *      operationId="deleteAplication",
     *     tags={"Postulaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la postulación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Postulación eliminada correctamente"),
     *     @OA\Response(response=404, description="Postulación no encontrada")
     * )
     */
    public function deleteAplication($id)
    {
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json(['message' => 'No se encontró la aplicación'], 404);
        }
        $aplication->delete();
        return response()->json(['message' => 'Aplicación eliminada correctamente'], 200);
    }
}
