<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\RegistroImagenes;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

class jobsController extends Controller
{
    public function allJobs()
    {
        $job = Trabajo::all();
        return response()->json([
            'jobs' => $job
        ], 200);
    }
    public function createJob(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Titulo'    => 'required|string|max:100',
            'Descripcion' => 'nullable|string|max:255',
            'Requisitos' => 'nullable|string|max:255',
            'Competencias' => 'nullable|string|max:255',
            'Ubicacion' => 'nullable|string|max:255',
            'Salario' => 'nullable|numeric',
            'Categoria' => 'nullable|string|max:100',
            'Modalidad' => 'required|string|max:100',
            'Fecha_Inicio'  => 'required|date',
            'Fecha_Final' => 'required|date',
            'Duracion' => 'nullable|integer',
            'Tipo' => 'required|string|max:100',
            'Id_Empresa' => 'nullable|integer',
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $archivosGuardados = [];
        $manager = ImageManager::withDriver(new GdDriver());
        foreach ($request->file('imagenes') as $imagen) {
            $nombre = Str::random(10) . '.webp';
            $ruta = storage_path('app/public/imagenes/' . $nombre);

            $image = $manager->read($imagen);
            $image->toWebp()->save($ruta);
            $archivosGuardados[] = $nombre;
        }

        $job = Trabajo::create([
            'Titulo'    => $request->Titulo,
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
            'Nombre_Imagen' => $archivosGuardados,
            'Tipo' => $request->Tipo,
            'Id_Empresa' => $request->Id_Empresa,
        ]);
        return response()->json([
            'job' => $job
        ], 201);
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
        $job = Trabajo::find($id);
        if (!$job) {
            return response()->json([
                'mensaje' => 'Trabajo no encontrado'
            ], 404);
        }
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
            'Titulo'    => 'required|string|max:100',
            'Descripcion' => 'nullable|string|max:255',
            'Requisitos' => 'nullable|string|max:255',
            'Competencias' => 'nullable|string|max:255',
            'Ubicacion' => 'nullable|string|max:255',
            'Salario' => 'nullable|numeric',
            'Categoria' => 'nullable|string|max:100',
            'Modalidad' => 'required|string|max:100',
            'Fecha_Inicio'  => 'required|date',
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
        if ($request->hasFile('imagenes')) {
            $archivosGuardados = [];
            $manager = ImageManager::withDriver(new GdDriver());
            foreach ($request->file('imagenes') as $imagen) {
                $nombre = Str::random(10) . '.webp';
                $ruta = storage_path('app/public/imagenes/' . $nombre);

                $image = $manager->read($imagen);
                $image->toWebp()->save($ruta);
                $archivosGuardados[] = $nombre;
            }
        }
        $job->update([
            'Titulo'    => $request->Titulo,
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
            'Nombre_Imagen' => $archivosGuardados ,
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
