<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\DialogflowService;

/**
 * @OA\Tag(
 *     name="Chatbot",
 *     description="Endpoints para interactuar con el chatbot"
 * )
 */
class ChatbotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/chatbot/message",
     *     summary="Enviar mensaje al chatbot",
     *     tags={"Chatbot"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="¿Qué servicios ofrecen?"),
     *             @OA\Property(property="session_id", type="string", example="unique-session-id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta del chatbot",
     *         @OA\JsonContent(
     *             @OA\Property(property="response", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */
    public function sendMessage(Request $request)
    {
        $accessToken = DialogflowService::getAccessToken();
        $message = $request->input('message');
        $sessionId = $request->input('session_id') ?? uniqid();

        $response = Http::withToken($accessToken)->post("https://dialogflow.googleapis.com/v2/projects/" . env('DIALOGFLOW_PROJECT_ID') . "/agent/sessions/{$sessionId}:detectIntent", [
            'queryInput' => [
                'text' => [
                    'text' => $message,
                    'languageCode' => 'es'
                ]
            ]
        ]);

        return $response->json();
    }
}
