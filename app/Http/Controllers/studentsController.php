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
            'Carnet'=> 'required|string|max:100',
            'Nombre'=> 'required|string|max:100',
            'Apellido'=> 'required|string|max:100',
            'Correo'=> 'required|email|max:100',
            'Carrera'=> 'required|string|max:100',
            'Id_Usuario'=> 'required|integer',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $student = Estudiante::create([
            'Nro_Registro' => $request->Nro_Registro,
            'Carnet' => $request->Carnet,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'Correo' => $request->Correo,
            'Carrera' => $request->Carrera,
            'Id_Usuario' => $request->Id_Usuario,
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
            'Carnet'=> 'required|string|max:100',
            'Nombre'=> 'required|string|max:100',
            'Apellido'=> 'required|string|max:100',
            'Correo'=> 'required|email|max:100',
            'Carrera'=> 'required|string|max:100',
            'Id_Usuario'=> 'required|integer',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $student->update($request->all());
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
