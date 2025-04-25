<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        return response()->json([
            'roles' => $roles
        ], 200);
    }

    public function show($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }
        return response()->json([
            'rol' => $rol
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $rol = Rol::create([
            'Nombre' => $request->Nombre,
        ]);

        return response()->json([
            'rol' => $rol,
            'message' => 'Rol creado exitosamente'
        ], 201);
    }

    /**
     * Actualizar un rol existente
     */
    public function update(Request $request, $id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $rol->update([
            'Nombre' => $request->Nombre,
        ]);

        return response()->json([
            'rol' => $rol,
            'message' => 'Rol actualizado exitosamente'
        ], 200);
    }

    /**
     * Eliminar un rol
     */
    public function destroy($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $rol->delete();
        return response()->json([
            'message' => 'Rol eliminado exitosamente'
        ], 200);
    }

    /**
     * Asignar rol a un usuario
     */
    public function assignRolToUser(Request $request)
    {
        // Validación de datos
        $validate = Validator::make($request->all(), [
            'Id_Usuario' => 'required|integer|exists:usuario,Id_Usuario',
            'Id_Rol' => 'required|integer|exists:rol,Id_Rol',
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
    
        // Buscar el usuario
        $usuario = Usuario::find($request->Id_Usuario);
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    
        // Buscar el rol
        $rol = Rol::find($request->Id_Rol);
        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }
    
        // Asignar el rol al usuario
        $usuario->Id_Rol = $request->Id_Rol;
        $usuario->save();
    
        return response()->json([
            'message' => 'Rol asignado correctamente',
            'usuario' => $usuario
        ], 200);
    }
}
