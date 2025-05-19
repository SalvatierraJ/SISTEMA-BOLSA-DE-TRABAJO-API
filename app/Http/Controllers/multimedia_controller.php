<?php

namespace App\Http\Controllers;
use App\Models\Multimedia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Cloudinary\Api\Upload\UploadApi;

/**
 * @OA\Tag(
 *     name="Multimedia",
 *     description="Endpoints para gestionar archivos multimedia"
 * )
 */
class multimedia_controller extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/multimedia/{id}",
     *     summary="Eliminar un archivo multimedia específico",
     *     operationId="deleteMultimedia",
     *     tags={"Multimedia"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del archivo multimedia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo multimedia encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="multimedia", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo multimedia no encontrado"
     *     )
     * )
     */
    public function deleteMultimedia($id) {
        try {
            $multimedia = Multimedia::where('Id_Multimedia', $id)
            ->orWhere('Id_Usuario', $id)
            ->orWhere('Id_Trabajo', $id)
            ->firstOrFail();

            if ($multimedia->Nombre) {
                $url = $multimedia->Nombre;
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? null;

                if ($path) {
                    $segments = explode('/', ltrim($path, '/'));
                    $publicIdWithExt = array_pop($segments);
                    $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);
                    $folder = implode('/', $segments);

                    $fullPublicId = $folder . '/' . $publicId;

                    $resourceType = (str_contains($url, '/video/')) ? 'video' : 'image';

                    (new UploadApi())->destroy($fullPublicId, [
                        'resource_type' => $resourceType
                    ]);
                }
            }

            $multimedia->delete();
            return response()->json([
                'message' => 'Multimedia deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting multimedia',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/multimedia/{id}",
     *     summary="Obtener un archivo multimedia específico",
     *     operationId="getMultimedia",
     *     tags={"Multimedia"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del archivo multimedia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo multimedia encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="multimedia", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo multimedia no encontrado"
     *     )
     * )
     */
    public function getMultimedia($id) {
        try {
            $multimedia = multimedia::findOrFail($id);
            return response()->json([
                'multimedia' => $multimedia
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching multimedia',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/multimedia",
     *     summary="Crear un nuevo archivo multimedia",
     *     operationId="createMultimedia",
     *     tags={"Multimedia"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Titulo", "Tipo"},
     *             @OA\Property(property="Titulo", type="string", example="Logo UTEPSA"),
     *             @OA\Property(property="Descripcion", type="string", example="Logo oficial de la universidad"),
     *             @OA\Property(property="Tipo", type="string", enum={"heroicon", "Video"}, example="heroicon"),
     *             @OA\Property(property="Estado", type="string", example="Activo"),
     *             @OA\Property(property="Nombre", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Archivo multimedia creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="multimedia", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al procesar el archivo"
     *     )
     * )
     */
    public function createMultimedia(Request $request) {
        $rules = [
            'Titulo' => 'required|string|max:255',
            'Descripcion' => 'nullable|string',
            'Tipo' => 'required|string|max:50',
            'Estado' => 'nullable|string|max:50',
        ];

        if ($request->input('Tipo') === 'heroicon') {
            $rules['Nombre'] = 'required|file|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($request->input('Tipo') === 'Video') {
            $rules['Nombre'] = 'required|file|mimes:mp4,mov,avi|max:51200';
        } else {
            $rules['Nombre'] = 'nullable|string|max:255';
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $data = $request->except('Nombre');

        if ($request->hasFile('Nombre') && $request->input('Tipo') === 'heroicon') {
            $response = (new UploadApi())->upload($request->file('Nombre')->getRealPath(), [
                'folder' => 'Logo_Utepsa',
                'resource_type' => 'image'
            ]);


            try {
                $data['Nombre'] = $response['secure_url'] ?? null;
            } catch (Exception $e) {
                return response()->json(['message' => 'Error al procesar la imagen', 'error' => $e->getMessage()], 500);
            }
        }  elseif ($request->hasFile('Nombre') && $request->input('Tipo') === 'Video') {
            try {
                $response = (new UploadApi())->upload($request->file('Nombre')->getRealPath(), [
                    'folder' => 'videos_utepsa',
                    'resource_type' => 'video'
                ]);

                $data['Nombre'] = $response['secure_url'] ?? null;

                if (!$data['Nombre']) {
                    return response()->json(['message' => 'Error: Cloudinary no devolvió una URL'], 500);
                }

            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Error al subir el video a Cloudinary',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        else {
            $data['Nombre'] = null;
        }

        $multimedia = multimedia::create($data);

        return response()->json($multimedia, 201);
    }

    public function getMultimediaByType($tipo) {
        try {
            $multimedia = multimedia::where('Tipo', $tipo)->get();
            return response()->json([
                'multimedia' => $multimedia
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching multimedia by type',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStateMultimedia(Request $request, $id) {
        try {
            $multimedia = multimedia::findOrFail($id);
            $multimedia->Estado = $multimedia->Estado === 'Activo' ? 'Inactivo' : 'Activo';
            $multimedia->save();

            return response()->json([
                'message' => 'Estado del multimedia actualizado exitosamente',
                'nuevo_estado' => $multimedia->Estado
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado del multimedia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
