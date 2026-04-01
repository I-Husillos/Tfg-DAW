<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        private AiService $aiService
    ) {}

    public function ask(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1500'],
        ]);

        $history  = session()->get('ai_chat_history', []);
        $response = $this->aiService->ask($request->message, $history);

        if (!$response) {
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo obtener respuesta. ¿Está Ollama activo?',
            ], 500);
        }

        $history[] = ['role' => 'user',      'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $response];

        session()->put('ai_chat_history', array_slice($history, -20));

        return response()->json([
            'ok'       => true,
            'response' => $response,
        ]);
    }

    public function clear()
    {
        session()->forget('ai_chat_history');

        return response()->json(['ok' => true]);
    }
}