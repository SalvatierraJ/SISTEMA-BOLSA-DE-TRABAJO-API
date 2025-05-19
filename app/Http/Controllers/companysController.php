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

/**
 * @OA\Tag(
 *     name="Empresas",
 *     description="Endpoints para gestionar empresas"
 * )
 */
class companysController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/empresas",
     *     summary="Obtener todas las empresas",
     *     tags={"Empresas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas",
     *         @OA\JsonContent(
     *             @OA\Property(property="companys", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getAlllComapanys(){
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
    /**
     * @OA\Get(
     *     path="/api/empresas/usuario",
     *     summary="Obtener empresa del usuario autenticado",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Empresa encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function getCompanyByUser(){
        $user = Auth::user();
        $company = Empresa::with('usuario', 'multimedia', 'telefonos')->where('Id_Usuario', $user->Id_Usuario)->first();
        return response()->json([
            'company' => $company
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/empresas/sectores",
     *     summary="Obtener todos los sectores",
     *     tags={"Empresas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sectores",
     *         @OA\JsonContent(
     *             @OA\Property(property="sector", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getSector(){
        $sector = Sector::all();
        return response()->json([
            'sector' => $sector
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/empresas",
     *     summary="Crear una nueva empresa",
     *     tags={"Empresas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nombre", "Id_Sector", "Correo"},
     *             @OA\Property(property="Nombre", type="string", example="Empresa XYZ"),
     *             @OA\Property(property="Id_Sector", type="integer", example=1),
     *             @OA\Property(property="Correo", type="string", format="email", example="contacto@empresa.com"),
     *             @OA\Property(property="Descripcion", type="string", example="Descripción de la empresa"),
     *             @OA\Property(property="Redes_Sociales", type="array", @OA\Items(
     *                 @OA\Property(property="red", type="string", example="Facebook"),
     *                 @OA\Property(property="enlace", type="string", format="uri", example="https://facebook.com/empresa")
     *             )),
     *             @OA\Property(property="Direccion", type="string", example="Calle Principal #123"),
     *             @OA\Property(property="Contacto", type="string", example="Juan Pérez"),
     *             @OA\Property(property="Direccion_Web", type="string", format="uri", example="https://empresa.com"),
     *             @OA\Property(property="imagen", type="string", format="binary"),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="integer", example=76543210))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="company", type="object"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="La empresa ya existe"
     *     )
     * )
     */
    public function createCompany(Request $request){
        $redesSociales = $request->input('Redes_Sociales');
        if (is_string($redesSociales)) {
            $request->merge(['Redes_Sociales' => json_decode($redesSociales, true)]);
        }

        $validate = Validator::make($request->all(), [
            'Nombre'             => 'required|string|max:100',
            'Id_Sector'          => 'required|integer',
            'Correo'             => 'required|email|max:100|unique:usuario,Usuario',
            'Descripcion'        => 'nullable|string',
            'Redes_Sociales'     => 'nullable|array',
            'Redes_Sociales.*.red' => 'nullable|string|max:100',
            'Redes_Sociales.*.enlace' => 'nullable|string|max:256|url',
            'Direccion'          => 'nullable|string|max:255',
            'Contacto'           => 'nullable|string|max:100',
            'Direccion_Web'      => 'nullable|string|max:255',
            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'telefonos'          => 'nullable|array',
            'telefonos.*'        => 'sometimes|nullable|integer|digits_between:7,15'
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
                'Nombre'        => $request->Nombre,
                'Id_Sector'     => $request->Id_Sector,
                'Correo'        => $request->Correo,
                'Direccion'     => $request->Direccion,
                'Redes_Sociales' => json_encode($request->Redes_Sociales),
                'Descripcion'   => $request->Descripcion,
                'Contacto'      => $request->Contacto,
                'Direccion_Web' => $request->Direccion_Web,
                'Id_Usuario'    => $user->Id_Usuario
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
                    'Tipo'       => 'logo',
                    'Nombre'     => $response['secure_url']
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
    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}/imagen",
     *     summary="Eliminar imagen de una empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function deleteImageCompany(Request $request, $id){
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
              $publicId = $this->getPublicIdFromUrl($media->Nombre);
              if ($publicId) {
              $this->eliminarDeCloudinary($publicId);
              }
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

    /**
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     summary="Obtener una empresa específica",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function getCompany($id){
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
    /**
     * @OA\Put(
     *     path="/api/empresas/{id}",
     *     summary="Actualizar una empresa existente",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Nombre", type="string", example="Empresa XYZ"),
     *             @OA\Property(property="Id_Sector", type="integer", example=1),
     *             @OA\Property(property="Correo", type="string", format="email", example="contacto@empresa.com"),
     *             @OA\Property(property="Descripcion", type="string", example="Descripción actualizada"),
     *             @OA\Property(property="Redes_Sociales", type="array", @OA\Items(
     *                 @OA\Property(property="red", type="string", example="Facebook"),
     *                 @OA\Property(property="enlace", type="string", format="uri", example="https://facebook.com/empresa")
     *             )),
     *             @OA\Property(property="Direccion", type="string", example="Nueva Dirección #456"),
     *             @OA\Property(property="Contacto", type="string", example="María García"),
     *             @OA\Property(property="Direccion_Web", type="string", format="uri", example="https://empresa.com"),
     *             @OA\Property(property="imagen", type="string", format="binary"),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="integer", example=76543210))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function updateCompany(Request $request, $id){
        $company = Empresa::with('usuario.multimedia', 'telefonos')->find($id);
        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $redesSociales = $request->input('Redes_Sociales');
        if (is_string($redesSociales)) {
            $request->merge(['Redes_Sociales' => json_decode($redesSociales, true)]);
        }
        $validate = Validator::make($request->all(), [
            'Nombre' => 'sometimes|required|string|max:100',
            'Id_Sector' => 'sometimes|required|integer',
            'Correo' => 'sometimes|required|email|max:100|unique:usuario,Usuario,' . $company->usuario->Id_Usuario . ',Id_Usuario',
            'Direccion' => 'sometimes|nullable|string|max:255',
            'Contacto' => 'sometimes|nullable|string|max:100',
            'Direccion_Web' => 'sometimes|nullable|string|max:255',
            'Redes_Sociales'      => 'sometimes|nullable|array',
            'Redes_Sociales.*.red' => 'nullable|string|max:100',
            'Redes_Sociales.*.enlace' => 'nullable|string|max:256|url',
            'Descripcion' => 'sometimes|nullable|string|max:65535',
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
                'Direccion_Web',
                'Descripcion',
                'Redes_Sociales'
            ]));
            $company->Redes_Sociales = json_encode($request->Redes_Sociales);
            $company->save();
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
    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     summary="Eliminar una empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function deleteCompany($id){
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
    /**
     * @OA\Put(
     *     path="/api/empresas/{id}/toggle",
     *     summary="Cambiar el estado de una empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado cambiado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string", enum={"Activo", "Inactivo"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function toggleCompanyStatus($id){
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
    /**
     * @OA\Delete(
     *     path="/api/empresas/{id}/telefonos/{phoneId}",
     *     summary="Eliminar un teléfono de una empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="phoneId",
     *         in="path",
     *         required=true,
     *         description="ID del teléfono",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teléfono eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa o teléfono no encontrado"
     *     )
     * )
     */
    public function deletePhone($id, $phoneId){
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
    /**
     * @OA\Put(
     *     path="/api/empresas/{id}/credentials",
     *     summary="Actualizar credenciales de una empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Usuario", type="string", example="nuevo_usuario"),
     *             @OA\Property(property="Clave", type="string", example="nueva_clave"),
     *             @OA\Property(property="Estado", type="string", enum={"Activo", "Inactivo"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Credenciales actualizadas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function updateCredentials(Request $request, $id){
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
    //Cloudinary
    private function getPublicIdFromUrl($url) {
        if (!$url) return null;
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? null;
        if ($path) {
           $segments = explode('/', ltrim($path, '/'));
            $publicIdWithExt = array_pop($segments);
            return pathinfo($publicIdWithExt, PATHINFO_FILENAME);
        }
      return null;
    }
    private function eliminarDeCloudinary($publicId){
        $uploadApi = new UploadApi();
        $uploadApi->destroy($publicId);
    }
}
