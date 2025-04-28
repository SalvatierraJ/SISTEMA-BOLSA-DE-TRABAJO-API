<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class studentsController extends Controller
{
    public function allStudents()
    {
        $students = Estudiante::all();
        return response()->json([
            'students' => $students
        ], 200);
    }
    
    public function createStudent(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'Nro_Registro' => 'required|integer',
            'Id_Persona' => 'required|integer|exists:persona,Id_Persona|unique:estudiante,Id_Persona',
        ]);
        
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        
        $student = Estudiante::create([
            'Nro_Registro' => $request->Nro_Registro,
            'Id_Persona' => $request->Id_Persona,
        ]);
        
        return response()->json([
            'student' => $student
        ], 201);
    }
    
    public function getStudent($id)
    {
        $student = Estudiante::find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        return response()->json([
            'student' => $student
        ], 200);
    }
    
    public function updateStudent(Request $request, $id)
    {
        $student = Estudiante::find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        
        $validate = Validator::make($request->all(), [
            'Nro_Registro' => 'required|integer',
            'Id_Persona' => 'required|integer|exists:persona,Id_Persona|unique:estudiante,Id_Persona,'.$id.',Id_Estudiante',
        ]);
        
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        
        $student->update([
            'Nro_Registro' => $request->Nro_Registro,
            'Id_Persona' => $request->Id_Persona,
        ]);
        
        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student
        ], 200);
    }
    
    public function deleteStudent($id)
    {
        $student = Estudiante::find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        $student->delete();
        return response()->json([
            'message' => 'Student deleted successfully'
        ], 200);
    }
}
