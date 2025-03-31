<?php

// Настройки теста
$subject = "Физика";  
$topic = "Законы Ньютона";  
$num_questions = 20;  

$stream = false;
$url = "https://openrouter.ai/api/v1/chat/completions";

$api_key = "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";

// Формируем промпт
$prompt = "Составь тест по предмету '$subject' на тему '$topic'. 
Тест должен состоять из $num_questions вопросов. 
У каждого вопроса 4 варианта ответа с указателями (A, B, C, D), где один вариант правильный. 
Оформи ответ в формате JSON-массива, где каждый элемент — объект с ключами: 
'question' (текст вопроса), 'options': ['A', 'B', 'C', 'D'], 'correct' (буква правильный ответа не должно быть всегда одинаковым), 
'explanation' (объяснение правильного ответа или решение задачи с использованием формул или общепринятых правил).";

$data = [
    "temperature" => 0.8,
    "messages" => [
        ["role" => "user", "content" => $prompt]
    ],
    "model" => "deepseek/deepseek-r1",
    "stream" => $stream,
    "frequency_penalty" => 0,
    "max_tokens" => 6000
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die("Ошибка при выполнении запроса: " . $response);
}

echo "<pre>";
print_r($response);
echo "</pre>";

$response_data = json_decode($response, true);
if (!$response_data || !isset($response_data["choices"][0]["message"]["content"])) {
    die("Ошибка: некорректный JSON-ответ.");
}

// Достаём JSON-ответ от AI
$test_json = $response_data["choices"][0]["message"]["content"];

// Убираем ненужные кавычки ```json ... ```
$test_json = trim($test_json);
if (substr($test_json, 0, 7) === "```json") { 
    $test_json = substr($test_json, 7, -3);
}

// Преобразуем JSON в PHP-массив
$test_data = json_decode($test_json, true);

if (!$test_data) {
    die("Ошибка JSON-декодирования: " . json_last_error_msg());
}

// Проверяем, является ли JSON массивом
if (!is_array($test_data)) {
    die("Ошибка: JSON-ответ AI не является массивом вопросов.");
}

// Выводим тест
echo "📘 Тест по $subject (Тема: $topic) 📘\n\n";
foreach ($test_data as $index => $question) {  
    echo ($index + 1) . ". " . $question["question"] . "\n";
    foreach ($question["options"] as $option) {
        echo "   $option\n";
    }
    echo "✅ Правильный ответ: " . $question["correct"] . "\n\n";
    echo "✅ Правильный ответ: " . $question["explanation"] . "\n\n";

}

// Сохраняем тест в файл
file_put_contents("generated_test.json", json_encode($test_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Тест успешно сохранён в 'generated_test.json'\n";

?>
