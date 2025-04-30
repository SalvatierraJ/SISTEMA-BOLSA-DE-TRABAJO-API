<?php

namespace App\Http\Controllers;
use App\Models\multimedia;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

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
    public function updateMultimedia(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'Titulo' => 'sometimes|string|max:255',
        'Descripcion' => 'nullable|string',
        'Estado' => 'sometimes|string|max:50',
        'Nombre' => 'sometimes|nullable|file|mimes:jpeg,png,jpg,webp|max:2048|string|url|max:255', // Para imagen o URL de video
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $multimedia = Multimedia::findOrFail($id);

        $dataToUpdate = $request->only(['Titulo', 'Descripcion', 'Estado', 'Tipo']);

        // Actualización condicional para Hero Icons
        if ($multimedia->Tipo === 'heroicon' && $request->hasFile('Nombre')) {
            $file = $request->file('Nombre');
            $carpetaBase = 'heroicon';
            $carpetaGuardar = $carpetaBase;
            $nombreWebp = Str::random(40) . '.webp';
            $rutaRelativa = 'storage/' . $carpetaGuardar . '/' . $nombreWebp;
            $rutaCompleta = public_path($rutaRelativa);

            if (!Storage::disk('public')->exists($carpetaGuardar)) {
                Storage::disk('public')->makeDirectory($carpetaGuardar, 0755, true);
            }

            $manager = new ImageManager(new GdDriver());
            try {
                $image = $manager->read($file)->toWebp(80);
                $image->save($rutaCompleta);

                // Eliminar la imagen antigua si existe
                if ($multimedia->Nombre && Storage::disk('public')->exists(str_replace('storage/', '', $multimedia->Nombre))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $multimedia->Nombre));
                }

                $dataToUpdate['Nombre'] = $rutaRelativa;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al procesar la nueva imagen', 'error' => $e->getMessage()], 500);
            }
        }
        // Actualización condicional para Videos (usando el campo 'Nombre' del request)
        elseif ($multimedia->Tipo === 'Video' && $request->filled('Nombre')) {
            $url = $request->input('Nombre');
            $videoId = null;

            // Extraer ID de YouTube
            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                parse_str(parse_url($url, PHP_URL_QUERY), $params);
                $videoId = $params['v'] ?? null;
                if (!$videoId && preg_match('/\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    $videoId = $matches[1];
                } elseif (!$videoId && preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    $videoId = $matches[1];
                }

                if ($videoId) {
                    $dataToUpdate['Nombre'] = 'https://www.youtube.com/embed/' . $videoId;
                } else {
                    $dataToUpdate['Nombre'] = $url; // Guardar la URL original si no se pudo extraer el ID
                }
            }
            // Extraer ID de Vimeo
            elseif (strpos($url, 'vimeo.com') !== false) {
                $path = parse_url($url, PHP_URL_PATH);
                $videoId = trim($path, '/');
                if (is_numeric($videoId)) {
                    $dataToUpdate['Nombre'] = 'https://player.vimeo.com/video/' . $videoId;
                } else {
                    $dataToUpdate['Nombre'] = $url; // Guardar la URL original si no es un ID numérico
                }
            }
            else {
                $dataToUpdate['Nombre'] = $url; // Guardar la URL tal cual si no es de YouTube o Vimeo
            }
        }
        // Para otros tipos, simplemente actualizar el 'Nombre' si se proporciona
        elseif ($request->filled('Nombre')) {
            $dataToUpdate['Nombre'] = $request->input('Nombre');
        }

        $multimedia->update($dataToUpdate);

        return response()->json([
            'message' => 'Multimedia actualizado exitosamente',
            'multimedia' => $multimedia->fresh()
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Multimedia no encontrado'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar el multimedia',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function createMultimedia(Request $request){
        $rules = [
            'Titulo' => 'required|string|max:255',
            'Descripcion' => 'nullable|string',
            'Tipo' => 'required|string|max:50',
            'Estado' => 'nullable|string|max:50',
        ];
    
        // Validación condicional para el campo Nombre
        if ($request->input('Tipo') === 'heroicon') {
            $rules['Nombre'] = 'required|file|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($request->input('Tipo') === 'Video') {
            $rules['Nombre'] = 'nullable|string|url|max:255'; // Permite strings que sean URLs
        } else {
            $rules['Nombre'] = 'nullable|string|max:255'; // Para otros tipos, permite un string
        }
    
        $validate = Validator::make($request->all(), $rules);
    
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
    
        $data = $request->except('Nombre');
    
        // Procesamiento de la imagen
        if ($request->hasFile('Nombre') && $request->input('Tipo') === 'heroicon') {
            $carpetaBase = 'heroicon'; // Nombre de la carpeta dentro de public/storage
            $carpetaGuardar = $carpetaBase;
            $nombreWebp = Str::random(40) . '.webp';
            $rutaRelativa = 'storage/' . $carpetaGuardar . '/' . $nombreWebp;
            $rutaCompleta = public_path($rutaRelativa);
    
            if (!Storage::disk('public')->exists($carpetaGuardar)) {
                Storage::disk('public')->makeDirectory($carpetaGuardar, 0755, true);
            }
    
            $manager = new ImageManager(new GdDriver());
            $imagen = $request->file('Nombre');
            try {
                $image = $manager->read($imagen)->toWebp(80);
                $image->save($rutaCompleta);
                $data['Nombre'] = $rutaRelativa;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al procesar la imagen', 'error' => $e->getMessage()], 500);
            }
        } elseif ($request->input('Tipo') === 'Video' && $request->filled('Nombre')) {
            $url = $request->input('Nombre');
            $videoId = null;
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $videoId = $params['v'] ?? null;
            if (!$videoId && preg_match('/\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (!$videoId && preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                $videoId = $matches[1];
            }

            if ($videoId) {
                $data['Nombre'] = 'https://www.youtube.com/embed/' . $videoId;
            } else {
                $data['Nombre'] = $url; // Guardar la URL original si no se pudo extraer el ID
            }
        }
        else {
            $data['Nombre'] = $url; // Guardar la URL tal cual si no es de YouTube o Vimeo
        }
        } else {
            $data['Nombre'] = null;
        }
    
        $multimedia = Multimedia::create($data);
    
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
            $multimedia = Multimedia::findOrFail($id);
    
            $multimedia->Estado = ($multimedia->Estado === 'Activo') ? 'Inactivo' : 'Activo';
            $multimedia->save();
    
            return response()->json([
                'message' => 'Estado del multimedia actualizado exitosamente',
                'nuevo_estado' => $multimedia->Estado // Opcional: devolver el nuevo estado
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Multimedia no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado del multimedia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}