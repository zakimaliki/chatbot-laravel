<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Throwable;
use App\Models\ChatHistory;

class ChatController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function chat(Request $request)
    {
        try {
            return response()->stream(function () use ($request) {
                $client = new Client([
                    'http_errors' => false, // Prevent Guzzle from throwing exceptions for HTTP errors
                ]);

                $maxRetries = 3;
                $attempt = 0;

                while ($attempt < $maxRetries) {
                    $response = $client->post('https://api.openai.com/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => 'gpt-4o-mini',
                            'messages' => [
                                ['role' => 'user', 'content' => $request->input('message')]
                            ],
                            'stream' => true
                        ],
                        'stream' => true
                    ]);

                    if ($response->getStatusCode() === 429) {
                        $attempt++;
                        if ($attempt < $maxRetries) {
                            sleep(2 * $attempt); // Exponential backoff
                            continue;
                        }
                        echo "data: " . json_encode(['error' => "Rate limit reached. Please try again later."]) . "\n\n";
                        ob_flush();
                        flush();
                        return;
                    }

                    // Memproses response streaming
                    $body = $response->getBody()->detach(); // Convert Guzzle stream to resource
                    $fullResponse = ''; // Menyimpan respons lengkap

                    while (!feof($body)) {
                        $line = trim(fgets($body)); // Membaca per baris

                        if (empty($line)) continue;

                        if (str_starts_with($line, 'data: ')) {
                            $data = substr($line, 6);                       
                            
                            if ($data === "[DONE]") break;

                            $json = json_decode($data, true);
                            $content = $json['choices'][0]['delta']['content'] ?? '';

                            if (!empty($content)) {
                                $fullResponse .= $content; 
                                echo "data: " . json_encode(['data' => $content]) . "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }

                    // Simpan ke database
                    $user = auth()->user();
                    if ($user) {
                        ChatHistory::create([
                            'user_id' => $user->id,
                            'user_message' => $request->input('message'),
                            'ai_response' => $fullResponse,
                        ]);
                    }

                    break; // Keluar dari loop retry jika sukses
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no'
            ]);
        } catch (Throwable $e) {
            echo "data: " . json_encode(['error' => "An error occurred while processing your request. Please try again later."]) . "\n\n";
            ob_flush();
            flush();
        }
    }

    public function history()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $history = ChatHistory::where('user_id', $user->id)
            ->latest()
            ->get(['user_message', 'ai_response', 'created_at']);

        return response()->json($history);
    }
}