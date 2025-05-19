<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Telefono;
use App\Models\Usuario;
use App\Models\Multimedia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Endpoints para gestionar usuarios"
 * )
 */
class usersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/usuarios",
     *     summary="Obtener todos los usuarios",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios",
     *         @OA\JsonContent(
     *             @OA\Property(property="users", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function allUsers()
    {
        $user = Usuario::with(['rol', 'personas'])
            ->get();
        return response()->json([
            'users' => $user
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/usuarios/administrador",
     *     summary="Crear un nuevo usuario administrador",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"CI", "Nombre", "Apellido1", "Telefonos", "Correo", "Id_Rol"},
     *             @OA\Property(property="CI", type="integer", example=12345678),
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido1", type="string", example="Pérez"),
     *             @OA\Property(property="Apellido2", type="string", example="García"),
     *             @OA\Property(property="Genero", type="boolean", example=true),
     *             @OA\Property(property="Telefonos", type="array", @OA\Items(type="integer"), example={76543210}),
     *             @OA\Property(property="Correo", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="Id_Rol", type="integer", example=1),
     *             @OA\Property(property="Id_Carrera", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario administrador creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="persona", type="object"),
     *             @OA\Property(property="usuario", type="object"),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */
    public function createUserAdministrator(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'CI' => 'required|integer|unique:persona,CI',
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'Genero' => 'nullable|boolean',
            'Telefonos' => 'required|array|min:1',
            'Telefonos.*' => 'sometimes|integer|digits_between:7,15',
            'Correo' => 'required|email|max:100|unique:persona,Correo',
            'Id_Rol' => 'required|integer',
            'Id_Carrera' => 'nullable|integer|exists:carrera,Id_Carrera'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $usuario = Usuario::create([
                'Usuario' => $request->CI,
                'Clave' => bcrypt(12345678),
                'Id_Rol' => $request->Id_Rol,
                'Estado' => 'Activo'
            ]);

            $persona = Persona::create([
                'Nombre' => $request->Nombre,
                'Apellido1' => $request->Apellido1,
                'Apellido2' => $request->Apellido2,
                'CI' => $request->CI,
                'Genero' => $request->Genero,
                'Correo' => $request->Correo,
                'Id_Usuario' => $usuario->Id_Usuario
            ]);

            $telefonos = [];
            if ($request->has('Telefonos') && !empty($request->Telefonos)) {
                foreach ($request->Telefonos as $numero) {
                    if ($numero) {
                        $telefono = Telefono::create([
                            'numero' => $numero,
                            'Id_Persona' => $persona->Id_Persona
                        ]);
                        $telefonos[] = $telefono;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Estudiante registrado exitosamente',
                'persona' => $persona,
                'usuario' => $usuario,
                'telefonos' => $telefonos
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al registrar el estudiante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/usuarios/administrador/{id}",
     *     summary="Actualizar un usuario administrador",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido1", type="string", example="Pérez"),
     *             @OA\Property(property="Apellido2", type="string", example="García"),
     *             @OA\Property(property="CI", type="integer", example=12345678),
     *             @OA\Property(property="Genero", type="boolean", example=true),
     *             @OA\Property(property="Correo", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="Id_Rol", type="integer", example=1),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="integer"), example={76543210})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="persona", type="object"),
     *             @OA\Property(property="usuario", type="object"),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */
    public function updateUserAdministrator(Request $request, $id)
    {

        $persona = Persona::find($id);
        $user = Usuario::find($persona->Id_Usuario);
        if (!$persona || !$user) {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'CI' => [
                'sometimes',
                'integer',
                Rule::unique('persona', 'CI')->ignore($persona->Id_Persona, 'Id_Persona')
            ],
            'Nombre' => 'sometimes|string|max:100',
            'Apellido1' => 'sometimes|string|max:100',
            'Apellido2' => 'sometimes|string|max:100',
            'Genero' => 'sometimes|boolean',
            'telefonos' => 'sometimes|array|min:1',
            'telefonos.*' => [
                'required_with:telefonos',
                'numeric',
                'digits_between:7,15'
            ],
            'Correo' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('persona', 'Correo')->ignore($persona->Id_Persona, 'Id_Persona')
            ],
            'Id_Rol' => 'sometimes|integer'
        ]);


        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $updateUser = collect($request->only([
                'Id_Rol'
            ]))->filter(fn($val) => !is_null($val))->toArray();
            $updateData = collect($request->only([
                'Nombre', 'Apellido1', 'Apellido2', 'CI', 'Genero', 'Correo'
            ]))->filter(fn($val) => !is_null($val))->toArray();

            $persona->update($updateData);
            $user->update($updateUser);


            Telefono::where('Id_Persona', $persona->Id_Persona)->delete();

            $telefonos = [];
            if ($request->has('telefonos') && !empty($request->telefonos)) {
                foreach ($request->telefonos as $numero) {
                    if ($numero) {
                        $telefono = Telefono::create([
                            'numero' => $numero,
                            'Id_Persona' => $persona->Id_Persona
                        ]);
                        $telefonos[] = $telefono;
                    }
                }
            }


            DB::commit();

            return response()->json([
                'message' => 'Estudiante actualizado exitosamente',
                'persona' => $persona,
                'usuario' => $persona->usuario,
                'telefonos' => $telefonos
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al actualizar el estudiante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/usuarios/administrador/{id}/credentials",
     *     summary="Actualizar credenciales de un usuario administrador",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Usuario", type="string", example="juan123"),
     *             @OA\Property(property="Id_Rol", type="integer", example=1),
     *             @OA\Property(property="Clave", type="string", example="password123"),
     *             @OA\Property(property="Clave_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Credenciales actualizadas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function updateAdministratorCredentials(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Usuario' => 'sometimes|string|unique:usuario,Usuario',
            'Clave' => 'sometimes|string|min:6',
            'Estado' => 'sometimes|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateUser = collect($request->only([
                'Usuario','Clave','Estado'
            ]))->filter(fn($val) => !is_null($val))->toArray();

            $usuario->update($updateUser);

            DB::commit();

            return response()->json([
                'message' => 'Credenciales del Administrador actualizadas exitosamente',
                'usuario' => $usuario
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al actualizar las credenciales del estudiante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleAdministratorStatus($id)
    {
        $user = Usuario::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $newStatus = $user->Estado === 'Activo' ? 'Inactivo' : 'Activo';
            $user->update([
                'Estado' => $newStatus
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Administrator status updated successfully',
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


    public function getUser(Request $request)
    {
        $user = Usuario::with(['personas.estudiantes.carreras','multimedia'])->where('Id_Usuario', $request->user()->Id_Usuario)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }
    public function updateUser(Request $request, $id)
    {
        $user = Usuario::find($id);
        $validato = Validator::make($request->all(), [
            'Usuario' => 'sometimes|min:3|max:100',
            'Id_Rol' => 'sometimes|integer',
            'Clave' => 'sometimes|min:6|confirmed'
        ]);
        if ($validato->fails()) {
            return response()->json([
                'errors' => $validato->errors()
            ], 422);
        }
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->update($request->only(['Usuario', 'Clave', 'Id_Rol']));
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/usuarios/{id}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function deleteUser($id)
    {
        $user = Usuario::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/usuarios/upload-image",
     *     summary="Subir imagen de perfil o banner",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Imagen", "Tipo"},
     *             @OA\Property(property="Imagen", type="string", format="binary"),
     *             @OA\Property(property="Tipo", type="string", enum={"Perfil", "Banner"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen subida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="multimedia", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al subir la imagen"
     *     )
     * )
     */
    public function uploadImage(Request $request){
        $user = Auth::user();
        $validate = Validator::make($request->all(), [
            'Imagen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'Tipo' => 'required|string|in:Perfil,Banner' // Asegúrate de que Tipo solo sea 'Perfil' o 'Banner'
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        if($request->hasFile('Imagen')){
            try{
                $file = $request->file('Imagen');
                $response = (new UploadApi())->upload($file->getRealPath(), [
                    'folder' => 'Utepsa_Estudiantes',
                    'resource_type' => 'image',
                ]);
                $imagePath = $response['secure_url'];
                $tipo = $request->input('Tipo');

                $Multimedia = Multimedia::where('Id_Usuario', $user->Id_Usuario)->where('Tipo', $tipo)->first();

                if($Multimedia){
                    // Actualizar la entrada existente
                    $this->eliminarDeCloudinary($Multimedia->Nombre); // Eliminar la imagen anterior de Cloudinary
                    $Multimedia->update(['Nombre' => $imagePath]);
                    $message = 'Imagen actualizada exitosamente';
                }else{
                    // Crear una nueva entrada
                    $Multimedia = Multimedia::create([
                        'Nombre' => $imagePath,
                        'Id_Usuario' => $user->Id_Usuario,
                        'Tipo' => $tipo,
                    ]);
                    $message = 'Imagen subida exitosamente';
                }
                return response()->json(['message' => $message, 'multimedia' => $Multimedia], 200);
            }catch(\Exception $e){
                return response()->json(['message' => 'Error al subir la imagen', 'error' => $e->getMessage()], 500);
            }
        }
    }
    private function eliminarDeCloudinary($publicId){
        $uploadApi = new UploadApi();
        try {
            $uploadApi->destroy($publicId);
            return response()->json([
                'message' => 'Imagen eliminada de Cloudinary'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar de Cloudinary: " . $e->getMessage());
        }
    }
}
