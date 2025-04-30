<?php

namespace App\Http\Controllers;
use App\Models\multimedia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;

class multimedia_controller extends Controller
{
    public function deleteMultimedia($id) {
        try {
            $multimedia = multimedia::findOrFail($id);
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
            $carpetaBase = 'heroicon';
            $nombreWebp = Str::random(40) . '.webp';
            $rutaRelativa = 'storage/' . $carpetaBase . '/' . $nombreWebp;
            $rutaCompleta = public_path($rutaRelativa);

            if (!Storage::disk('public')->exists($carpetaBase)) {
                Storage::disk('public')->makeDirectory($carpetaBase);
            }

            $manager = new ImageManager(new GdDriver());
            try {
                $image = $manager->read($request->file('Nombre'))->toWebp(80);
                $image->save($rutaCompleta);
                $data['Nombre'] = $rutaRelativa;
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
