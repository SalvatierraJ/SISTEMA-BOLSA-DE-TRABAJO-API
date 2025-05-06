<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{
    public function getAll()
    {
        $roles = Rol::all();
        return response()->json([
            'roles' => $roles
        ], 200);
    }

    public function getRollWith($id)
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

    public function createRole(Request $request)
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

    public function updateRole(Request $request, $id)
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

    public function deleteRole($id)
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
}
