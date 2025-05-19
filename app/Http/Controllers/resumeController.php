<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Currículums",
 *     description="Endpoints para gestionar currículums"
 * )
 */
class resumeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/curriculums",
     *     summary="Obtener todos los currículums",
     *     tags={"Currículums"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de currículums",
     *         @OA\JsonContent(
     *             @OA\Property(property="resumes", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function allResumes(){
        $resumes = Curriculum::all();
        return response()->json([
            'resumes' => $resumes
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/curriculums",
     *     summary="Crear un nuevo currículum",
     *     tags={"Currículums"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Id_Estudiante", "Descripcion", "Habilidades"},
     *             @OA\Property(property="Id_Estudiante", type="integer", example=1),
     *             @OA\Property(property="Descripcion", type="string", example="Desarrollador web con experiencia en Laravel"),
     *             @OA\Property(property="Habilidades", type="string", example="PHP, Laravel, MySQL, JavaScript"),
     *             @OA\Property(property="Certificados", type="string", example="AWS Certified Developer"),
     *             @OA\Property(property="Experiencia", type="string", example="3 años como desarrollador web"),
     *             @OA\Property(property="Idiomas", type="string", example="Español nativo, Inglés intermedio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Currículum creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="resume", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/curriculums/{id}",
     *     summary="Obtener un currículum específico",
     *     tags={"Currículums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del currículum",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currículum encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="resume", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Currículum no encontrado"
     *     )
     * )
     */
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
