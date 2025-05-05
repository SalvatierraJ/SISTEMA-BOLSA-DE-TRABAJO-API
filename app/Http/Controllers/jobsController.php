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
use Illuminate\Support\Facades\DB;
use Cloudinary\Api\Upload\UploadApi;
class jobsController extends Controller
{
    public function lastTenJob(){
        $job= Trabajo::with(['empresa','multimedia'])
            ->orderBy('created_at','desc')
            ->take(10)
            ->get();
        return response()->json([
            'jobs'=> $job
        ], 200);
    }
    public function allJobs()
    {
        $imagenes = Multimedia::all();
        $job = Trabajo::all();
        $jobs = [];
        $trabajos = [];
        foreach($job as $trabajo) {
            foreach($imagenes as $imagen) {
                if ($trabajo->Id_Trabajo == $imagen->Id_Trabajo) {
                    $trabajo->Nombre_Imagen = asset($imagen->Nombre);
                    array_push($jobs, $trabajo);
                    break;
                }
            }
        }
        foreach($jobs as $trabajo) {
            $empresa = Empresa::find($trabajo->Id_Empresa);
            if ($empresa) {
                $trabajo->Nombre_Empresa = $empresa->Nombre;
                array_push($trabajos, $trabajo);
            } }
            $jobs = $trabajos;

        return response()->json([
            'jobs' => $jobs
        ], 200);
    }
    public function createJob(Request $request)
    {
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
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
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
                'Id_Empresa' => $request->Id_Empresa
            ]);

            $archivosGuardados = [];
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $response = (new UploadApi())->upload($imagen->getRealPath(), [
                        'folder' => 'trabajos_utepsa',
                        'resource_type' => 'image'
                    ]);

                    Multimedia::create([
                        'Nombre' => $response['secure_url'],
                        'Tipo' => 'image',
                        'Id_Trabajo' => $job->Id_Trabajo,
                        'public_id' => $response['public_id']
                    ]);

                    $archivosGuardados[] = $response['secure_url'];
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Trabajo creado exitosamente',
                'job' => $job,
                'imagenes' => $archivosGuardados
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al crear el trabajo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function uploadImageJob(Request $request, $id)
    {
        $trabajo = Trabajo::findOrFail($id);
        if (!$trabajo) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }
        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $archivosGuardados = [];
        $manager = ImageManager::withDriver(new GdDriver());

        foreach ($request->file('imagenes') as $imagen) {
            $nombre = Str::random(10) . '.webp';
            $ruta = storage_path('app/public/imagenes/' . $nombre);

            $image = $manager->read($imagen);
            $image->toWebp()->save($ruta);

            $archivosGuardados[] = $nombre;
        }

        $trabajo->Nombre_Imagen = $archivosGuardados;
        $trabajo->save();

        return response()->json([
            'mensaje' => 'Imágenes subidas correctamente',
            'archivos' => $archivosGuardados
        ], 200);
    }


    public function deleteImageJob(Request $request, $id)
    {
        $request->validate([
            'nombre_imagen' => 'required|string'
        ]);

        $trabajo = Trabajo::findOrFail($id);
        $nombreImagen = $request->nombre_imagen;


        if (Storage::disk('public')->exists('imagenes/' . $nombreImagen)) {
            Storage::disk('public')->delete('imagenes/' . $nombreImagen);
        }

        $imagenes = $trabajo->Nombre_Imagen;
        $imagenes = array_filter($imagenes, fn($img) => $img !== $nombreImagen);

        $trabajo->Nombre_Imagen = array_values($imagenes);
        $trabajo->save();

        return response()->json([
            'mensaje' => 'Imagen eliminada correctamente',
            'imagenes_restantes' => $trabajo->Nombre_Imagen
        ], 200);
    }
    public function getjob($id)
    {
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
    public function getjobsByType($type){
        $job = Trabajo::with(['empresa', 'multimedia'])
             ->where('Tipo_Trabajo', $type)
             ->get();
        return response()->json([
            'job' => $job
        ], 200);

    }
    public function updateJob(Request $request, $id)
    {
        $job = Trabajo::find($id);

        if (!$job) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Titulo' => 'required|string|max:100',
            'Descripcion' => 'nullable|string|max:255',
            'Requisitos' => 'nullable|string|max:255',
            'Competencias' => 'nullable|string|max:255',
            'Ubicacion' => 'nullable|string|max:255',
            'Salario' => 'nullable|numeric',
            'Categoria' => 'nullable|string|max:100',
            'Modalidad' => 'required|string|max:100',
            'Fecha_Inicio' => 'required|date',
            'Fecha_Final' => 'required|date',
            'Duracion' => 'nullable|integer',
            'Tipo' => 'required|string|max:100',
            'Id_Empresa' => 'nullable|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $archivosGuardados = []; // Inicializamos el arreglo de archivos guardados

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $nombre = Str::random(10) . '.webp';
                $ruta = $imagen->storeAs('imagenes', $nombre, 'public');


                $imagePath = 'storage/imagenes/' . $nombre; // Ruta de la imagen

                $multimedia = Multimedia::where('Id_Trabajo', $job->Id_Trabajo)->first();
                if ($multimedia) {
                    $multimedia->Nombre = $imagePath; // Actualizamos la ruta de la imagen
                    $multimedia->Tipo = 'webp';
                    $multimedia->save();
                } else {
                    Multimedia::create([
                        'Nombre' => $imagePath,
                        'Tipo' => 'webp',
                        'Id_Trabajo' => $job->Id_Trabajo
                    ]);
                }

                $archivosGuardados[] = $imagePath;
            }
        }
            $job->update([
            'Titulo' => $request->Titulo,
            'Descripcion' => $request->Descripcion,
            'Requisitos' => $request->Requisitos,
            'Competencias' => $request->Competencias,
            'Ubicacion' => $request->Ubicacion,
            'Salario' => $request->Salario,
            'Categoria' => $request->Categoria,
            'Modalidad' => $request->Modalidad,
            'Fecha_Inicio' => $request->Fecha_Inicio,
            'Fecha_Final' => $request->Fecha_Final,
            'Duracion' => $request->Duracion,
            'Nombre_Imagen' => json_encode($archivosGuardados), // Guardamos como JSON si es un array
            'Tipo' => $request->Tipo,
            'Id_Empresa' => $request->Id_Empresa
        ]);

        return response()->json([
            'job' => $job
        ], 200);
    }

    public function deleteJob($id)
    {
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
    public function getJobsByCompany($id)
    {
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


}
