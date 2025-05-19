<?php

namespace App\Http\Controllers;

use App\Models\Testimonio;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Testimonios",
 *     description="Endpoints para gestionar testimonios"
 * )
 */
class testimonialsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/testimonios",
     *     summary="Obtener todos los testimonios",
     *     tags={"Testimonios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de testimonios",
     *         @OA\JsonContent(
     *             @OA\Property(property="testimonials", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function allTestimonials()
    {
        $testimonials = Testimonio::with(['usuario.multimedia','usuario.personas.estudiantes.carreras'])
            ->get();
        return response()->json([
            'testimonials' => $testimonials
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/testimonios/{id}/toggle",
     *     summary="Cambiar el estado de un testimonio",
     *     tags={"Testimonios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del testimonio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado cambiado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string"),
     *             @OA\Property(property="estado", type="string", enum={"Activo", "Inactivo"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Testimonio no encontrado"
     *     )
     * )
     */
    public function toggleEstado($id)
    {

        $testimonial = Testimonio::find($id);

        if (!$testimonial) {
            return response()->json(['error' => 'Testimonio no encontrado'], 404);
        }

        $nuevoEstado = $testimonial->Estado === 'Activo' ? 'Inactivo' : 'Activo';
        $testimonial->update(['Estado' => $nuevoEstado]);

        return response()->json([
            'mensaje' => "Estado cambiado a {$nuevoEstado}",
            'estado' => $nuevoEstado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/testimonios",
     *     summary="Crear un nuevo testimonio",
     *     tags={"Testimonios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Titulo", "Comentario"},
     *             @OA\Property(property="Titulo", type="string", example="Excelente experiencia"),
     *             @OA\Property(property="Comentario", type="string", example="La plataforma me ayudó a encontrar mi primer trabajo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Testimonio creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="testimonial", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function createTestimonial(Request $request)
    {
        $user = Auth::user();
        $validate = Validator::make($request->all(), [
            'Titulo' => 'required|string',
            'Comentario' => 'required|string',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $testimonial = Testimonio::create([
            'Id_Usuario' => $user->Id_Usuario,
            'Titulo' => $request->Titulo,
            'Comentario' => $request->Comentario,
        ]);
        return response()->json([
            'testimonial' => $testimonial
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/testimonios/{id}",
     *     summary="Obtener un testimonio específico",
     *     tags={"Testimonios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del testimonio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Testimonio encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="testimonial", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Testimonio no encontrado"
     *     )
     * )
     */
    public function getTestimonial($id)
    {
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        return response()->json([
            'testimonial' => $testimonial
        ], 200);
    }

    public function updateTestimonial(Request $request, $id)
    {
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        $validate = Validator::make($request->all(), [
            'Id_Estudiante' => 'required|integer',
            'Id_Empresa' => 'required|integer',
            'Comentario' => 'required|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $testimonial->update($request->all());
        return response()->json([
            'message' => 'Testimonio actualizado correctamente',
            'testimonial' => $testimonial
        ], 200);
    }

    public function deleteTestimonial($id)
    {
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        $testimonial->delete();
        return response()->json([
            'message' => 'Testimonio eliminado correctamente'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/testimonios/validate",
     *     summary="Validar contenido de un testimonio",
     *     tags={"Testimonios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Titulo", "Comentario"},
     *             @OA\Property(property="Titulo", type="string", example="Excelente experiencia"),
     *             @OA\Property(property="Comentario", type="string", example="La plataforma me ayudó a encontrar mi primer trabajo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentario validado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string"),
     *             @OA\Property(property="puntajes", type="object",
     *                 @OA\Property(property="toxicidad", type="number"),
     *                 @OA\Property(property="insulto", type="number"),
     *                 @OA\Property(property="profanidad", type="number")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Contenido inapropiado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */
    public function analizarComentario(Request $request)
    {
        $request->validate([
            'Titulo' => 'required|string|max:1000',
            'Comentario' => 'required|string|max:1000',

        ]);

        $apiKey = config('services.perspective.key');
        $textoAnalizar = $request->Titulo . '. ' . $request->Comentario;
        $response = Http::post("https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key={$apiKey}", [
            'comment' => ['text' => $textoAnalizar],
            'languages' => ['es'],
            'requestedAttributes' => [
                'TOXICITY' => new \stdClass(),
                'INSULT' => new \stdClass(),
                'PROFANITY' => new \stdClass(),
            ],
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Error al analizar el comentario.',
                'details' => $response->json()
            ], 500);
        }

        $result = $response->json();

        $toxicidad = $result['attributeScores']['TOXICITY']['summaryScore']['value'] ?? 0;
        $insulto = $result['attributeScores']['INSULT']['summaryScore']['value'] ?? 0;
        $profanidad = $result['attributeScores']['PROFANITY']['summaryScore']['value'] ?? 0;

        $limiteMaximoNegativo = 0.3;

        if (
            $toxicidad > $limiteMaximoNegativo ||
            $insulto > $limiteMaximoNegativo ||
            $profanidad > $limiteMaximoNegativo
        ) {
            return response()->json([
                'error' => 'El comentario debe ser positivo y respetuoso. Por favor, intenta redactarlo de forma más constructiva.'
            ], 422);
        }

        return response()->json([
            'mensaje' => 'Comentario aceptado y considerado positivo.',
            'puntajes' => [
                'toxicidad' => $toxicidad,
                'insulto' => $insulto,
                'profanidad' => $profanidad,
            ]
        ]);
    }
}
