<?php

$stream = false;
$url = "https://proxy.tune.app/chat/completions";
$api_key = "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"; // НЕ храните API-ключ в коде!

$data = [
    "temperature" => 0.8,
    "messages" => [
        [
            "role" => "user",
            "content" => "Составь тест по теме 'Физика', состоящий из 1 вопроса и 4 вариантов ответа, где один вариант правильный. Обозначь варианты ответа буквами A, B, C, D."
        ]
    ],
    "model" => "deepseek/deepseek-r1",
    "stream" => $stream,
    "frequency_penalty" => 0,
    "max_tokens" => 900
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

echo $response;

?>
