<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class resumeController extends Controller
{
    public function allResumes(){
        $resumes = Curriculum::all();
        return response()->json([
            'resumes' => $resumes
        ], 200);
    }
    public function createResume(Request $request){
        $validate = Validator::make($request->all(), [
            'Id_Estudiante' => 'required|integer',
            'Descripcion' => 'required|string|max:255',
            'Habilidades' => 'required|string|max:100',
            'Certificados' => 'nullable|string|max:255',
            'Experiencia' => 'nullable|string|max:255',
            'Idiomas' => 'nullable|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $resume = Curriculum::create([
            'Id_Estudiante' => $request->Id_Candidato,
            'Descripcion' => $request->Nombre,
            'Habilidades' => $request->Apellido,
            'Certificados' => $request->Telefono,
            'Experiencia' => $request->Email,
            'Idiomas' => $request->Direccion
        ]);
        return response()->json([
            'resume' => $resume
        ], 201);
    }
    public function getResume($id){
        $resume = Curriculum::find($id);
        if (!$resume) {
            return response()->json([
                'message' => 'No se encontró el curriculum'
            ], 404);
        }
        return response()->json([
            'resume' => $resume
        ], 200);
    }
    public function getResumeConfiguration($id){
        $resume = Curriculum::find($id);
        if (!$resume) {
            return response()->json([
                'message' => 'No se encontró el curriculum'
            ], 404);
        }
        return response()->json([$resume->configuracionCV
        ], 200);
    }
    public function updateResume(Request $request, $id){
        $resume = Curriculum::find($id);
        if (!$resume) {
            return response()->json([
                'message' => 'No se encontró el curriculum'
            ], 404);
        }
        $validate = Validator::make($request->all(), [
            'Id_Estudiante' => 'required|integer',
            'Descripcion' => 'required|string|max:255',
            'Habilidades' => 'required|string|max:100',
            'Certificados' => 'nullable|string|max:255',
            'Experiencia' => 'nullable|string|max:255',
            'Idiomas' => 'nullable|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $resume->update([
            'Id_Estudiante' => $request->Id_Candidato,
            'Descripcion' => $request->Nombre,
            'Habilidades' => $request->Apellido,
            'Certificados' => $request->Telefono,
            'Experiencia' => $request->Email,
            'Idiomas' => $request->Direccion
        ]);
        return response()->json([
            'resume' => $resume
        ], 200);
    }
    public function deleteResume($id){
        $resume = Curriculum::find($id);
        if (!$resume) {
            return response()->json([
                'message' => 'No se encontró el curriculum'
            ], 404);
        }
        $resume->delete();
        return response()->json([
            'message' => 'Curriculum eliminado correctamente'
        ], 200);
    }
}
