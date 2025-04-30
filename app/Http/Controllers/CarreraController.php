<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::all();
        return response()->json([
            'carreras' => $carreras
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100|unique:carrera,Nombre',
            'Descripcion' => 'nullable|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $carrera = Carrera::create([
            'Nombre' => $request->Nombre,
            'Descripcion' => $request->Descripcion
        ]);

        return response()->json([
            'message' => 'Carrera creada exitosamente',
            'carrera' => $carrera
        ], 201);
    }

    public function show($id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }
        return response()->json([
            'carrera' => $carrera
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:100|unique:carrera,Nombre,'.$id.',Id_Carrera',
            'Descripcion' => 'nullable|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $carrera->update($request->all());

        return response()->json([
            'message' => 'Carrera actualizada exitosamente',
            'carrera' => $carrera
        ], 200);
    }

    public function destroy($id)
    {
        $carrera = Carrera::find($id);
        if (!$carrera) {
            return response()->json([
                'message' => 'Carrera no encontrada'
            ], 404);
        }

        $carrera->delete();

        return response()->json([
            'message' => 'Carrera eliminada exitosamente'
        ], 200);
    }
}