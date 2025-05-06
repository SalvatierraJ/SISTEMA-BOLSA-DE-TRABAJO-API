<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Multimedia;
use App\Models\Rol;
use App\Models\Sector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use Cloudinary\Api\Upload\UploadApi;

class companysController extends Controller
{
    public function getAlllComapanys()
    {
        $companys = Empresa::with([
            'usuario.rol',
            'sector',
            'usuario.multimedia',
            'telefonos'
        ])->get();

        return response()->json([
            'companys' => $companys
        ], 200);
    }
    public function getCompanyByUser()
    {
        $user = Auth::user();
        $company = Empresa::with('usuario', 'multimedia', 'telefonos')->where('Id_Usuario', $user->Id_Usuario)->first();
        return response()->json([
            'company' => $company
        ], 200);
    }
    public function getSector()
    {
        $sector = Sector::all();
        return response()->json([
            'sector' => $sector
        ], 200);
    }
    public function createCompany(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre'         => 'required|string|max:100',
            'Id_Sector'      => 'required|integer',
            'Correo'         => 'required|email|max:100|unique:usuario,Usuario',
            'Descripcion'    => 'nullable|string|max:255',
            'Redes_Sociales' => 'nullable|string|max:256',
            'Direccion'      => 'nullable|string|max:255',
            'Contacto'       => 'nullable|string|max:100',
            'Direccion_Web'  => 'nullable|string|max:255',
            'imagen'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'telefonos'      => 'nullable|array',
            'telefonos.*'    => 'sometimes|nullable|integer|digits_between:7,15'
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

        try {
            DB::beginTransaction();

            $rol = Rol::where('Nombre', 'Empresa')->first();

            $user = Usuario::create([
                'Usuario' => $request->Correo,
                'Clave' => bcrypt(12345678),
                'Id_Rol' => $rol->Id_Rol,
                'Estado' => 'activo'
            ]);

            $empresa = Empresa::create([
                'Nombre'         => $request->Nombre,
                'Id_Sector'      => $request->Id_Sector,
                'Correo'         => $request->Correo,
                'Direccion'      => $request->Direccion,
                'Redes_Sociales' => $request->Redes_Sociales,
                'Descripcion'    => $request->Descripcion,
                'Contacto'       => $request->Contacto,
                'Direccion_Web'  => $request->Direccion_Web,
                'Id_Usuario'     => $user->Id_Usuario
            ]);

            if ($request->has('telefonos') && !empty($request->telefonos)) {
                foreach ($request->telefonos as $numero) {
                    if ($numero) {
                        $empresa->telefonos()->create([
                            'numero' => $numero
                        ]);
                    }
                }
            }

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $response = (new UploadApi())->upload($imagen->getRealPath(), [
                    'folder' => 'trabajos_utepsa',
                    'resource_type' => 'image'
                ]);

                Multimedia::create([
                    'Id_Usuario' => $user->Id_Usuario,
                    'Tipo' => 'logo',
                    'Nombre' => $response['secure_url']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Company and user created successfully',
                'company' => $empresa->load('telefonos'),
                'user' => [
                    'email' => $user->Usuario
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteImageCompany(Request $request, $id)
    {
        $company = Empresa::with('usuario.multimedia')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($company->usuario->multimedia as $media) {
                if ($media->Nombre) {
                    $this->eliminarDeCloudinary($media->Nombre);
                }
            }

            $company->usuario->multimedia()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Company images deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting company images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getCompany($id)
    {
        $company = Empresa::with('usuario.rol', 'sector', 'usuario.multimedia', 'telefonos', 'trabajos', 'trabajos.multimedia',)->find($id);
        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        return response()->json([
            'company' => $company
        ], 200);
    }
    public function updateCompany(Request $request, $id)
    {
        $company = Empresa::with('usuario.multimedia', 'telefonos')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'sometimes|required|string|max:100',
            'Id_Sector' => 'sometimes|required|integer',
            'Correo' => 'sometimes|required|email|max:100|unique:usuario,Usuario,' . $company->usuario->Id_Usuario . ',Id_Usuario',
            'Direccion' => 'sometimes|nullable|string|max:255',
            'Contacto' => 'sometimes|nullable|string|max:100',
            'Direccion_Web' => 'sometimes|nullable|string|max:255',
            'Redes_Sociales' => 'sometimes|nullable|string|max:256',
            'Descripcion' => 'sometimes|nullable|string|max:255',
            'imagen' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'telefonos' => 'nullable|array',
            'telefonos.*' => 'sometimes|nullable|integer|digits_between:7,15'
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
                'Id_Sector',
                'Correo',
                'Direccion',
                'Contacto',
                'Direccion_Web'
            ]));

            if ($request->has('Correo')) {
                $company->usuario->update([
                    'Usuario' => $request->Correo
                ]);
            }

            if ($request->has('telefonos')) {
                if ($company->telefonos->isNotEmpty()) {
                    $company->telefonos()->delete();
                }

                if (!empty($request->telefonos)) {
                    foreach ($request->telefonos as $numero) {
                        if ($numero) {
                            $company->telefonos()->create([
                                'numero' => $numero
                            ]);
                        }
                    }
                }
            }

            if ($request->hasFile('imagen')) {
                // Eliminar imágenes anteriores si existe
                if ($company->usuario->multimedia->isNotEmpty()) {
                    foreach ($company->usuario->multimedia as $media) {
                        $this->eliminarDeCloudinary($media->Nombre);
                        $media->delete();
                    }
                }

                $imagen = $request->file('imagen');

                try {
                    $response = (new UploadApi())->upload($imagen->getRealPath(), [
                        'folder' => 'trabajos_utepsa',
                        'resource_type' => 'image'
                    ]);

                    // Guardar el nuevo registro en la tabla Multimedia
                    Multimedia::create([
                        'Id_Usuario' => $company->usuario->Id_Usuario,
                        'Tipo' => 'logo',
                        'Nombre' => $response['secure_url']
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Error al subir la imagen a Cloudinary',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Company updated successfully',
                'company' => $company->load('usuario.rol', 'sector', 'usuario.multimedia', 'telefonos')
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
        $company = Empresa::with('usuario.multimedia', 'telefonos')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($company->usuario->multimedia as $media) {
                if ($media->Nombre && Storage::disk('public')->exists($media->Nombre)) {
                    Storage::disk('public')->delete($media->Nombre);
                }
            }


            if ($company->telefonos->isNotEmpty()) {
                $company->telefonos()->delete();
            }

            if ($company->usuario->multimedia->isNotEmpty()) {
                foreach ($company->usuario->multimedia as $media) {
                    $this->eliminarDeCloudinary($media->Nombre);
                    $media->delete();
                }
            }
            $company->delete();
            $company->usuario->delete();

            DB::commit();

            return response()->json([
                'message' => 'Company and associated data deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleCompanyStatus($id)
    {
        $company = Empresa::with('usuario')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $newStatus = $company->usuario->Estado === 'Activo' ? 'Inactivo' : 'Activo';
            $company->usuario->update([
                'Estado' => $newStatus
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company status updated successfully',
                'status' => $newStatus
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating company status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePhone($id, $phoneId)
    {
        $company = Empresa::with('telefonos')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $phone = $company->telefonos()->where('Id_Telefono', $phoneId)->first();

            if (!$phone) {
                return response()->json([
                    'message' => 'Phone number not found for this company'
                ], 404);
            }

            $phone->delete();

            DB::commit();

            return response()->json([
                'message' => 'Phone number deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting phone number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCredentials(Request $request, $id)
    {

        $company = Empresa::with('usuario')->find($id);

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Usuario' => 'sometimes|required|max:100|unique:usuario,Usuario,' . $company->usuario->Id_Usuario . ',Id_Usuario',
            'Clave' => 'sometimes|required|string|min:8',
            'Estado' => 'sometimes|required|string|in:Activo,Inactivo'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = [];

            if ($request->has('Usuario')) {
                $updateData['Usuario'] = $request->Usuario;
            }

            if ($request->has('Clave')) {
                $updateData['Clave'] = bcrypt($request->Clave);
            }

            if ($request->has('Estado')) {
                $updateData['Estado'] = $request->Estado;
            }

            if (!empty($updateData)) {
                $company->usuario->update($updateData);
            }

            DB::commit();

            return response()->json([
                'message' => 'Company credentials updated successfully',
                'company' => $company->load('usuario')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating company credentials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function eliminarDeCloudinary($url)
    {
        if (!$url) return;

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
}
