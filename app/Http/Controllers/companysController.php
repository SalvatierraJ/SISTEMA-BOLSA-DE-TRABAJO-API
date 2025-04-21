<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Multimedia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class companysController extends Controller
{
    public function getAlllComapanys()
    {
        $companny = Empresa::with('multimedia')->get();
        return response()->json([
            'companys' => $companny
        ], 200);
    }
    public function createCompany(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre'         => 'required|string|max:100',
            'Sector'         => 'required|string|max:100',
            'Correo'         => 'required|email|max:100',
            'Direccion'      => 'nullable|string|max:255',
            'Contacto'       => 'nullable|string|max:100',
            'Direccion_Web'  => 'nullable|string|max:255',
            'imagen'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existingCompany = Empresa::where('Nombre', $request->Nombre)->first();
        if ($existingCompany) {
            return response()->json([
                'message' => 'Company already exists'
            ], 409);
        }

        $empresa = Empresa::create([
            'Nombre'         => $request->Nombre,
            'Sector'         => $request->Sector,
            'Correo'         => $request->Correo,
            'Direccion'      => $request->Direccion,
            'Contacto'       => $request->Contacto,
            'Direccion_Web'  => $request->Direccion_Web
        ]);
        if ($request->hasFile('imagen')) {
            $manager = new ImageManager(new GdDriver());
            $imagen = $request->file('imagen');

            $nombre = Str::random(10) . '.webp';
            $rutaRelativa = 'storage/' . $nombre;
            $rutaCompleta = storage_path('app/public/' . $rutaRelativa);

            $image = $manager->read($imagen)->toWebp(80);
            $image->save($rutaCompleta);

            $multimedia = Multimedia::create([
                'id_empresa' => $empresa->Id_Empresa,
                'direccion'  => 'storage/' . $rutaRelativa
            ]);
        }

        return response()->json([
            'company'  => $empresa,
            'logo'     => $multimedia->direccion ?? null
        ], 201);
    }

    public function deleteImageCompany(Request $request, $id)
    {

        $company = Empresa::with('multimedia')->find($id);
        $nameImage = $company->multimedia[0]->direccion;


        if (Storage::disk('public')->exists($nameImage)) {
            Storage::disk('public')->delete($nameImage);
        }

        $company->multimedia()->delete();

        return response()->json([
            'mensaje' => 'Imagen eliminada correctamente'
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
        $company = Empresa::with('multimedia')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'sometimes|required|string|max:100',
            'Sector' => 'sometimes|nullable|string|max:100',
            'Correo' => 'sometimes|required|email|max:100',
            'Direccion' => 'sometimes|nullable|string|max:255',
            'Contacto' => 'sometimes|nullable|string|max:100',
            'Direccion_Web' => 'sometimes|nullable|string|max:255',
            'imagen' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $company->update($request->only([
                'Nombre',
                'Sector',
                'Correo',
                'Direccion',
                'Contacto',
                'Direccion_Web'
            ]));

            if ($request->hasFile('imagen')) {
                if ($company->multimedia->isNotEmpty()) {
                    foreach ($company->multimedia as $media) {
                        if ($media->direccion && Storage::disk('public')->exists($media->direccion)) {
                            Storage::disk('public')->delete($media->direccion);
                        }
                    }
                    $company->multimedia()->delete();
                }

                $manager = new ImageManager(new GdDriver());
                $imagen = $request->file('imagen');

                $nombre = Str::random(10) . '.webp';
                $rutaRelativa = 'storage/' . $nombre;
                $rutaCompleta = storage_path('app/public/' . $rutaRelativa);

                $image = $manager->read($imagen)->toWebp(80);
                $image->save($rutaCompleta);

                Multimedia::create([
                    'id_empresa' => $company->Id_Empresa,
                    'direccion' => 'storage/' . $rutaRelativa
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Company updated successfully',
                'company' => $company->load('multimedia')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCompany($id)
    {
        $companny = Empresa::with('multimedia')->find($id);
        if (!$companny) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $companny->multimedia()->delete();
        $companny->delete();
        return response()->json([
            'message' => 'Company deleted successfully'
        ], 200);
    }
}
