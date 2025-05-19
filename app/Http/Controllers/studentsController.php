<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\Telefono;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Estudiantes",
 *     description="Endpoints para gestionar estudiantes"
 * )
 */
class studentsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/estudiantes",
     *     summary="Obtener todos los estudiantes",
     *     tags={"Estudiantes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estudiantes",
     *         @OA\JsonContent(
     *             @OA\Property(property="students", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function allStudents()
    {
        $students = Estudiante::with(['persona.telefonos', 'persona.usuario', 'carreras'])
            ->get();
        return response()->json([
            'students' => $students
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/estudiantes",
     *     summary="Crear un nuevo estudiante",
     *     tags={"Estudiantes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nro_Registro", "CI", "Nombre", "Apellido1", "telefonos", "Correo", "Id_Carrera"},
     *             @OA\Property(property="Nro_Registro", type="string", example="2023001"),
     *             @OA\Property(property="CI", type="integer", example=12345678),
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido1", type="string", example="Pérez"),
     *             @OA\Property(property="Apellido2", type="string", example="García"),
     *             @OA\Property(property="Genero", type="boolean", example=true),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="integer", example=76543210)),
     *             @OA\Property(property="Correo", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="Id_Carrera", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estudiante creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="estudiante", type="object"),
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
     *         description="Error del servidor"
     *     )
     * )
     */
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
            'Correo' => 'required|email|max:100|unique:persona,Correo',
            'Id_Carrera' => 'required|integer|exists:carrera,Id_Carrera'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $rolEstudiante = DB::table('rol')->where('Nombre', 'Estudiante')->first();
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

            $estudiante->carreras()->attach($request->Id_Carrera);

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

    /**
     * @OA\Get(
     *     path="/api/estudiantes/{id}",
     *     summary="Obtener un estudiante específico",
     *     tags={"Estudiantes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estudiante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="student", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado"
     *     )
     * )
     */
    public function getStudent($id)
    {
        $student = Estudiante::with(['persona.telefonos', 'persona.usuario', 'carreras', 'curricula', 'postulacions'])
            ->find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Estudiante no encontrado'
            ], 404);
        }
        return response()->json([
            'student' => $student
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/estudiantes/{id}",
     *     summary="Actualizar un estudiante existente",
     *     tags={"Estudiantes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estudiante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Nro_Registro", "CI", "Nombre", "Apellido1", "telefonos", "Correo", "Id_Carrera"},
     *             @OA\Property(property="Nro_Registro", type="string", example="2023001"),
     *             @OA\Property(property="CI", type="integer", example=12345678),
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido1", type="string", example="Pérez"),
     *             @OA\Property(property="Apellido2", type="string", example="García"),
     *             @OA\Property(property="Genero", type="boolean", example=true),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="integer", example=76543210)),
     *             @OA\Property(property="Correo", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="Id_Carrera", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="estudiante", type="object"),
     *             @OA\Property(property="persona", type="object"),
     *             @OA\Property(property="usuario", type="object"),
     *             @OA\Property(property="telefonos", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function updateStudent(Request $request, $id)
    {
        $estudiante = Estudiante::find($id);
        if (!$estudiante) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Nro_Registro' => 'required|string|unique:estudiante,Nro_Registro,' . $id . ',Id_Estudiante',
            'CI' => 'required|integer|unique:persona,CI,' . $estudiante->persona->Id_Persona . ',Id_Persona',
            'Nombre' => 'required|string|max:100',
            'Apellido1' => 'required|string|max:100',
            'Apellido2' => 'nullable|string|max:100',
            'Genero' => 'nullable|boolean',
            'telefonos' => 'required|array|min:1',
            'telefonos.*' => 'required|integer|digits_between:7,15',
            'Correo' => 'required|email|max:100|unique:persona,Correo,' . $estudiante->persona->Id_Persona . ',Id_Persona',
            'Id_Carrera' => 'required|integer|exists:carrera,Id_Carrera'
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

            // Actualizar la relación con la carrera
            $estudiante->carreras()->sync([$request->Id_Carrera]);

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

    /**
     * @OA\Delete(
     *     path="/api/estudiantes/{id}",
     *     summary="Eliminar un estudiante",
     *     tags={"Estudiantes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estudiante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     */
    public function deleteStudent($id)
    {
        try {
            DB::beginTransaction();

            $student = Estudiante::find($id);
            if (!$student) {
                return response()->json([
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            // Eliminar relaciones
            $student->carreras()->detach();

            // Eliminar estudiante y sus relaciones
            $persona = Persona::find($student->Id_Persona);
            if ($persona) {
                Telefono::where('Id_Persona', $persona->Id_Persona)->delete();
                if ($persona->usuario) {
                    $persona->usuario->delete();
                }
                $persona->delete();
            }

            $student->delete();

            DB::commit();

            return response()->json([
                'message' => 'Estudiante eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al eliminar el estudiante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/estudiantes/{id}/credentials",
     *     summary="Actualizar credenciales de un estudiante",
     *     tags={"Estudiantes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estudiante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Usuario", "Clave"},
     *             @OA\Property(property="Usuario", type="string", example="nuevo_usuario"),
     *             @OA\Property(property="Clave", type="string", example="nueva_clave")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Credenciales actualizadas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="usuario", type="object")
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
    public function updateStudentCredentials(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'Usuario' => 'required|string|unique:usuario,Usuario',
            'Clave' => 'required|string|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $usuario->update([
                'Usuario' => $request->Usuario,
                'Clave' => bcrypt($request->Clave)
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Credenciales del estudiante actualizadas exitosamente',
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

    /**
     * @OA\Post(
     *     path="/api/estudiantes/curriculum",
     *     summary="Guardar currículum del estudiante",
     *     tags={"Estudiantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"formSettings"},
     *             @OA\Property(property="formSettings", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currículum guardado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="curriculum", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado"
     *     )
     * )
     */
    public function saveCurriculum(Request $request)
    {
        $user = Auth::user()->load(['rol', 'personas.estudiantes.carreras', 'personas.telefonos', 'testimonios']);

        $persona = $user->personas->first();
        $estudiante = $persona?->estudiantes->first();

        if (!$persona || !$estudiante) {
            return response()->json(['error' => 'No se encontró persona o estudiante'], 404);
        }

        if (Curriculum::where('Id_Estudiante', $estudiante->Id_Estudiante)->exists()) {
            $curriculum = Curriculum::update([
                'Configuracion_CV' => $request->formSettings
            ]);
        } else {
            $curriculum = Curriculum::create([
                'Configuracion_CV' => $request->formSettings,
                'Id_Estudiante' => $estudiante->Id_Estudiante
            ]);
        }

        return response()->json([
            'message' => 'Currículum guardado correctamente',
            'curriculum' => $curriculum
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/estudiantes/curriculum",
     *     summary="Obtener currículum del estudiante",
     *     tags={"Estudiantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Currículum encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="formSettings", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado"
     *     )
     * )
     */
    public function getCurriculumEstudiante()
    {
        $user = Auth::user()->load(['rol', 'personas.estudiantes.carreras', 'personas.telefonos', 'testimonios']);

        $persona = $user->personas->first();
        $estudiante = $persona?->estudiantes->first();
        if (!$persona || !$estudiante) {
            return response()->json(['error' => 'No se encontró persona o estudiante'], 404);
        }

        $curriculum = Curriculum::where('Id_Estudiante', $estudiante->Id_Estudiante)->first();

        return response()->json([
            'formSettings' => $curriculum->Configuracion_CV
        ], 200);
    }
}
