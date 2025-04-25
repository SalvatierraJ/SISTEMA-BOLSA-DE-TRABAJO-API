<?php

namespace App\Http\Controllers;

use App\Models\Telefono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TelefonoController extends Controller
{
    /**
     * Mostrar todos los teléfonos
     */
    public function index()
    {
        $telefonos = Telefono::all();
        return response()->json([
            'telefonos' => $telefonos
        ], 200);
    }

    /**
     * Mostrar un teléfono específico
     */
    public function show($id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }
        return response()->json([
            'telefono' => $telefono
        ], 200);
    }

    /**
     * Crear un nuevo teléfono
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Numero' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $telefono = Telefono::create([
            'Numero' => $request->Numero
        ]);

        return response()->json([
            'telefono' => $telefono,
            'message' => 'Teléfono creado exitosamente'
        ], 201);
    }

    /**
     * Actualizar un teléfono existente
     */
    public function update(Request $request, $id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Numero' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $telefono->update([
            'Numero' => $request->Numero
        ]);

        return response()->json([
            'telefono' => $telefono,
            'message' => 'Teléfono actualizado exitosamente'
        ], 200);
    }

    /**
     * Eliminar un teléfono
     */
    public function destroy($id)
    {
        $telefono = Telefono::find($id);
        if (!$telefono) {
            return response()->json([
                'message' => 'Teléfono no encontrado'
            ], 404);
        }

        $telefono->delete();
        return response()->json([
            'message' => 'Teléfono eliminado exitosamente'
        ], 200);
    }
}