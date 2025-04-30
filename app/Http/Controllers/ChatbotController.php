<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\DialogflowService;

class ChatbotController extends Controller
{
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
