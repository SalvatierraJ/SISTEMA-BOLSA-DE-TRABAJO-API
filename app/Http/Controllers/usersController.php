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

class usersController extends Controller
{
    public function allUsers()
    {
        $user = Usuario::with(['rol', 'personas'])
            ->get();
        return response()->json([
            'users' => $user
        ], 200);
    }

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