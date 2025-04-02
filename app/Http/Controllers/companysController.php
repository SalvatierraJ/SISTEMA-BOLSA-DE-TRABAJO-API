<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

class companysController extends Controller
{
    public function getAlllComapanys()
    {
        $companny = Empresa::all();
        return response()->json([
            'companys' => $companny
        ], 200);
    }
    public function createCompany(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
            'Sector' => 'nullable|string|max:100',
            'Correo' => 'required|email|max:100',
            'Direccion' => 'nullable|string|max:255',
            'Contacto' => 'nullable|string|max:100',
            'Direccion_Web' => 'nullable|string|max:255',
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $existingCompany = Empresa::where('Nombre', $request->input('Nombre'))->first();
        if ($existingCompany) {
            return response()->json([
                'message' => 'Company already exists'
            ], 409);
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
        $companny = Empresa::create([
            'Nombre' => $request->Nombre,
            'Sector' => $request->Sector,
            'Correo' => $request->Correo,
            'Direccion' => $request->Direccion,
            'Contacto' => $request->Contacto,
            'Direccion_Web' => $request->Direccion_Web,
            'logotipo' => $archivosGuardados
        ]);
        return response()->json([
            'company' => $companny
        ], 201);
    }
    public function uploadImageCompany(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);
        if (!$empresa) {
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

        $empresa->logotipo = $archivosGuardados;
        $empresa->save();

        return response()->json([
            'mensaje' => 'Imágenes subidas correctamente',
            'archivos' => $archivosGuardados
        ], 200);
    }
    public function deleteImageCompany(Request $request, $id)
    {
        $request->validate([
            'nombre_imagen' => 'required|string'
        ]);

        $company = Empresa::findOrFail($id);
        $nameImage = $request->nombre_imagen;


        if (Storage::disk('public')->exists('imagenes/' . $nameImage)) {
            Storage::disk('public')->delete('imagenes/' . $nameImage);
        }

        $images = $company->logotipo;
        $images = array_filter($images, fn($img) => $img !== $nameImage);

        $company->Nombre_Imagen = array_values($images);
        $company->save();

        return response()->json([
            'mensaje' => 'Imagen eliminada correctamente',
            'imagenes_restantes' => $company->logotipo
        ], 200);
    }
    public function getCompany($id)
    {
        $companny = Empresa::find($id);
        if (!$companny) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        return response()->json([
            'company' => $companny
        ], 200);
    }
    public function updateCompany(Request $request, $id)
    {
        $companny = Empresa::find($id);
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
            'Sector' => 'nullable|string|max:100',
            'Correo' => 'required|email|max:100',
            'Direccion' => 'nullable|string|max:255',
            'Contacto' => 'nullable|string|max:100',
            'Direccion_Web' => 'nullable|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        if (!$companny) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $companny->update($request->all());
        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $companny
        ], 200);
    }
    public function deleteCompany($id)
    {
        $companny = Empresa::find($id);
        if (!$companny) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $companny->delete();
        return response()->json([
            'message' => 'Company deleted successfully'
        ], 200);
    }
}
