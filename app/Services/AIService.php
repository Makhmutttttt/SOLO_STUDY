<?php
//AiService
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class AIService
{
    private $url;
    private $api_key;

    public function __construct()
    {
        $this->url = Config::get('services.ai.url');
        $this->api_key = Config::get('services.ai.api_key');
    }

    public function generateTest($subject, $topic, $num_questions)
    {
        $prompt = "Составь тест по предмету '$subject' на тему '$topic'. 
        Тест должен состоять из $num_questions вопросов. 
        У каждого вопроса 4 варианта ответа (A, B, C, D), где один правильный. 
        Ответ в JSON-формате: 
        [{'question': '...', 'options': ['A', 'B', 'C', 'D'], 'correct': 'A'}]";

        $response = Http::timeout(100)->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
        ])->post($this->url, [
            'temperature' => 0.8,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'model' => 'deepseek/deepseek-r1',
            'stream' => false,
            'max_tokens' => 3000
        ]);

        if ($response->failed()) {
            Log::error("Ошибка при отправке запроса в AI: " . $response->body());
            return null;
        }

        $data = $response->json();

        if (!isset($data["choices"][0]["message"]["content"])) {
            Log::error("AI не вернул ожидаемый JSON.");
            return null;
        }

        $test_json = trim($data["choices"][0]["message"]["content"]);

        // Исправленный JSON парсинг
        $test_json = preg_replace('/^```json|```$/', '', $test_json);

        $decoded = json_decode($test_json, true);
        if (!$decoded) {
            Log::error("Ошибка парсинга JSON: " . json_last_error_msg());
        }

        return $decoded;
    }
}
