<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\Telefono;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
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
            'Nro_Registro' => 'required|string|unique:estudiante,Nro_Registro',
            'CI' => 'required|integer|unique:persona,CI',
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'Genero' => 'nullable|boolean',
            'telefonos' => 'required|array|min:1',
            'telefonos.*' => 'required|integer|digits_between:7,15',
            'Correo' => 'required|email|max:100|unique:persona,Correo'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $rolEstudiante = DB::table('rol')->where('Nombre', 'estudiante')->first();
            if (!$rolEstudiante) {
                throw new \Exception('El rol de estudiante no está configurado en el sistema');
            }

            $usuario = Usuario::create([
                'Usuario' => $request->Nro_Registro,
                'Clave' => bcrypt($request->CI),
                'Id_Rol' => $rolEstudiante->Id_Rol,
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

            $estudiante = Estudiante::create([
                'Nro_Registro' => $request->Nro_Registro,
                'Id_Persona' => $persona->Id_Persona
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Estudiante registrado exitosamente',
                'estudiante' => $estudiante,
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
        $estudiante = Estudiante::find($id);
        if (!$estudiante) {
            return response()->json([
                'message' => 'Estudiante no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nro_Registro' => 'required|string|unique:estudiante,Nro_Registro,'.$id.',Id_Estudiante',
            'CI' => 'required|integer|unique:persona,CI,'.$estudiante->persona->Id_Persona.',Id_Persona',
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'Genero' => 'nullable|boolean',
            'telefonos' => 'required|array|min:1',
            'telefonos.*' => 'required|integer|digits_between:7,15',
            'Correo' => 'required|email|max:100|unique:persona,Correo,'.$estudiante->persona->Id_Persona.',Id_Persona'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Actualizar persona
            $persona = Persona::find($estudiante->Id_Persona);
            $persona->update([
                'Nombre' => $request->Nombre,
                'Apellido1' => $request->Apellido1,
                'Apellido2' => $request->Apellido2,
                'CI' => $request->CI,
                'Genero' => $request->Genero,
                'Correo' => $request->Correo
            ]);

            $estudiante->update([
                'Nro_Registro' => $request->Nro_Registro
            ]);

            if ($persona->usuario) {
                $persona->usuario->update([
                    'Usuario' => $request->Nro_Registro,
                    'Clave' => $request->has('CI') ? bcrypt($request->CI) : $persona->usuario->Clave
                ]);
            }

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
                'estudiante' => $estudiante,
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
