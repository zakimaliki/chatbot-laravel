<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                $client = new \GuzzleHttp\Client([
                    'http_errors' => false // Prevent Guzzle from throwing exceptions for HTTP errors
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
                            'store' => true,
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
                        echo "Rate limit reached. Please try again in a few moments.";
                        return;
                    }

                    // Process successful response
                    $body = $response->getBody();
                    
                    $fullResponse = ''; // Variabel untuk menyimpan respons lengkap

                    while (!$body->eof()) {
                        $line = trim($body->read(1024));
                        
                        if (empty($line)) continue;
                        
                        $lines = explode("\n", $line);
                        foreach ($lines as $line) {
                            if (str_starts_with($line, 'data: ')) {
                                $data = substr($line, 6);                       
                                
                                if ($data === "[DONE]") break 2;
                                
                                if ($data) {
                                    $json = json_decode($data, true);
                                    $content = $json['choices'][0]['delta']['content'] ?? '';
                                    if (!empty($content)) {
                                        $fullResponse .= $content; // Mengumpulkan semua konten
                                    }
                                }
                            }
                        }
                    }
                    
                    // Mengirimkan respons lengkap sebagai JSON
                    echo json_encode(['data' => $fullResponse]) . "\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();

                    $user = auth()->user(); 
                    ChatHistory::create([
                        'user_id' => $user->id,
                        'user_message' => $request->input('message'),
                        'ai_response' => $fullResponse,
                    ]);

                    break; // Break out of the retry loop after successful processing
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no'
            ]);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), '429')) {
                return "Rate limit reached. Please try again in a few moments.";
            }
            return "An error occurred while processing your request. Please try again later.";
        }
    }

    public function history()
    {

        $user = auth()->user();
        $history = ChatHistory::where('user_id', $user->id)->latest()->get();

        return response()->json($history);
    }
}