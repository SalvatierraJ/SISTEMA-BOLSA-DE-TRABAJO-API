<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use App\Models\Multimedia;
use App\Models\Empresa;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Trabajos",
 *     description="Endpoints para gestionar ofertas de trabajo"
 * )
 */
class jobsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/trabajos",
     *     summary="Crear una nueva oferta de trabajo",
     *     tags={"Trabajos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Titulo", "Descripcion", "Requisitos", "Competencia", "Ubicacion", "Modalidad", "Fecha_Inicio", "Estado", "Tipo_Trabajo", "Id_Empresa"},
     *             @OA\Property(property="Titulo", type="string", example="Desarrollador Web Senior"),
     *             @OA\Property(property="Descripcion", type="string", example="Buscamos un desarrollador web con experiencia en Laravel"),
     *             @OA\Property(property="Requisitos", type="string", example="3 años de experiencia en desarrollo web"),
     *             @OA\Property(property="Competencia", type="string", example="PHP, Laravel, MySQL"),
     *             @OA\Property(property="Ubicacion", type="string", example="Santa Cruz"),
     *             @OA\Property(property="Salario", type="number", example=5000),
     *             @OA\Property(property="Modalidad", type="string", example="Presencial"),
     *             @OA\Property(property="Fecha_Inicio", type="string", format="date", example="2024-05-01"),
     *             @OA\Property(property="Fecha_Fin", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="Duracion", type="string", example="6 meses"),
     *             @OA\Property(property="Estado", type="string", enum={"Activo", "Inactivo"}),
     *             @OA\Property(property="Tipo_Trabajo", type="integer", example=1),
     *             @OA\Property(property="Id_Empresa", type="integer", example=1),
     *             @OA\Property(property="imagen", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trabajo creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="trabajo", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function lastTenJob(){
        $job = Trabajo::with(['empresa', 'multimedia'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response()->json([
            'jobs' => $job
        ], 200);
    }
    public function allJobs(){
        $job = Trabajo::all();
        $trabajos = [];

        foreach ($job as $trabajo) {
            $imagen = Multimedia::where('Id_Trabajo', $trabajo->Id_Trabajo)->first();

            $trabajo->Nombre_Imagen = $imagen
                ? asset($imagen->Nombre)
                : asset("storage/imagenes/portales.webp");
            $trabajo->Id_Imagen = $imagen->Id_Multimedia ?? 0;

            $empresa = Empresa::find($trabajo->Id_Empresa);
            $trabajo->Nombre_Empresa = $empresa?->Nombre_Empresa ?? 'Desconocida';

            $trabajos[] = $trabajo;
        }


        return response()->json([
            'jobs' => $trabajos
        ], 200);
    }
    public function createJob(Request $request){
        $validate = Validator::make($request->all(), [
            'Titulo' => 'required|string|max:100',
            'Descripcion' => 'required|string|max:1000',
            'Requisitos' => 'required|string',
            'Competencia' => 'required|string',
            'Ubicacion' => 'required|string|max:255',
            'Salario' => 'nullable|numeric',
            'Modalidad' => 'required|string|max:100',
            'Fecha_Inicio' => 'required|date',
            'Fecha_Fin' => 'nullable|date',
            'Duracion' => 'nullable|string|max:100',
            'Estado' => 'required|string|in:Activo,Inactivo',
            'Tipo_Trabajo' => 'required|integer',
            'Id_Empresa' => 'required|integer|exists:empresa,Id_Empresa',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:5124',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $job = Trabajo::create([
                'Titulo' => $request->Titulo,
                'Descripcion' => $request->Descripcion,
                'Requisitos' => json_encode($request->Requisitos),
                'Competencia' => json_encode($request->Competencia),
                'Ubicacion' => $request->Ubicacion,
                'Salario' => $request->Salario,
                'Modalidad' => $request->Modalidad,
                'Fecha_Inicio' => $request->Fecha_Inicio,
                'Fecha_Fin' => $request->Fecha_Fin,
                'Duracion' => $request->Duracion,
                'Estado' => $request->Estado,
                'Tipo_Trabajo' => $request->Tipo_Trabajo,
                'Id_Empresa' => $request->Id_Empresa,
            ]);

            if ($request->hasFile('imagen')) {
                try {
                    $imagen = $request->file('imagen');
                    $response = (new UploadApi())->upload($request->file('imagen')->getRealPath(), [
                        'folder' => 'trabajos_utepsa',
                        'resource_type' => 'image',
                    ]);

                    Multimedia::create([
                        'Id_Trabajo' => $job->Id_Trabajo,
                        'Nombre' => $response['secure_url'],
                        'Tipo' => 'trabajo',
                        'Estado' => 'Activo',
                    ]);
                }catch(error $e){
                    DB::rollback();
                    return response()->json([
                        'message' => 'Error al subir la imagen a Cloudinary',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Trabajo creado exitosamente',
                'job' => $job
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al crear el trabajo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteImageJob(Request $request, $id){
        $job = Trabajo::with('multimedia')->find($id);

        if (!$job) {
            return response()->json([
                'message' => 'job not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($job->multimedia as $media) {
                if ($media->Nombre) {
                    $this->eliminarDeCloudinary($media->Nombre);
                }
            }

            $job->multimedia()->delete();

            DB::commit();

            return response()->json([
                'message' => 'job images deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting job images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getjob($id){
        $job = Trabajo::with(['empresa', 'multimedia'])
            ->where('Id_Trabajo', $id)
            ->orWhere('Id_Empresa', $id)
            ->first();
        if (!$job) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }
        return response()->json([
            'job' => $job
        ], 200);
    }
    public function updateJob(Request $request, $id){
        $job = Trabajo::with(['multimedia'])->find($id);

        if (!$job) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }
        $rules = [
            'Titulo' => 'sometimes|string|max:100',
            'Descripcion' => 'sometimes|string|max:1000',
            'Requisitos' => 'sometimes|string',
            'Competencia' => 'sometimes|string',
            'Ubicacion' => 'sometimes|string|max:255',
            'Salario' => 'sometimes|numeric',
            'Modalidad' => 'sometimes|string|max:100',
            'Fecha_Inicio' => 'sometimes|date',
            'Fecha_Fin' => 'sometimes|date',
            'Duracion' => 'sometimes|string|max:100',
            'Estado' => 'sometimes|string|in:Activo,Inactivo',
            'Tipo_Trabajo' => 'sometimes|integer',
            'Id_Empresa' => 'sometimes|integer|exists:empresa,Id_Empresa',
        ];
    if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpeg,png,jpg|max:5124';
        }

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $job->update($request->only([
                'Titulo',
                'Descripcion',
                'Requisitos',
                'Competencia',
                'Ubicacion',
                'Salario',
                'Modalidad',
                'Fecha_Inicio',
                'Fecha_Fin',
                'Duracion',
                'Estado',
                'Tipo_Trabajo',
                'Id_Empresa'
            ]));

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');

                try {
                    $response = (new UploadApi())->upload($imagen->getRealPath(), [
                        'folder' => 'trabajos_utepsa',
                        'resource_type' => 'image'
                    ]);

                    $multimedia = $job->multimedia()->first();

                    if ($multimedia) {
                        $this->eliminarDeCloudinary($multimedia->Nombre);
                        $multimedia->update(['Nombre' => $response['secure_url']]);
                    } else {
                        Multimedia::create([
                            'Id_Trabajo' => $job->Id_Trabajo,
                            'Tipo' => 'trabajo',
                            'Nombre' => $response['secure_url']
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'message' => 'Error al subir la imagen a Cloudinary',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            DB::commit();

            $job->load('multimedia');

            return response()->json([
                'job' => $job
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'mensaje' => 'Error al actualizar el trabajo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function uploadImageJob(Request $request, $id){
        $trabajo = Trabajo::with(['multimedia'])->findOrFail($id);
        if (!$trabajo) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }

        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        return response()->json([
            'mensaje' => 'Imágenes subidas correctamente',
            #'archivos' =>
        ], 200);
    }
    public function getjobsByType($type){
        $job = Trabajo::with(['empresa', 'multimedia'])
            ->where('Tipo_Trabajo', $type)
            ->get();
        return response()->json([
            'job' => $job
        ], 200);
    }
    public function deleteJob($id){
        Multimedia::where('Id_Trabajo', $id)->delete();

        $job = Trabajo::find($id);
        if (!$job) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }
        $job->delete();
        return response()->json([
            'mensaje' => 'Trabajo eliminado correctamente'
        ], 200);
    }
    public function getJobsByCompany($id){
        $jobs = Trabajo::where('Id_Empresa', $id)->get();
        if ($jobs->isEmpty()) {
            return response()->json([
                'mensaje' => 'No se encontraron trabajos para esta empresa'
            ], 404);
        }
        return response()->json([
            'jobs' => $jobs
        ], 200);
    }
    private function eliminarDeCloudinary($publicId){
        $uploadApi = new UploadApi();
        try {
            $uploadApi->destroy($publicId);
        } catch (\Exception $e) {
            Log::error("Error al eliminar de Cloudinary: " . $e->getMessage());
        }
    }
}
