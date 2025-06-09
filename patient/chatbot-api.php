<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$role = $input['role'] ?? '';
$prompt = $input['prompt'] ?? '';

if (!$role || !$prompt) {
    echo json_encode(['error' => 'Missing role or prompt.']);
    exit;
}

$secretKey = 'YOUR API KEY';
$endpoint = 'https://api.groq.com/openai/v1/chat/completions';

$data = [
    'messages' => [
        ['role' => 'system', 'content' => $role],
        ['role' => 'user', 'content' => $prompt]
    ],
    'model' => 'llama3-8b-8192'
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $secretKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    echo json_encode(['error' => 'Curl error: ' . $error]);
    exit;
}
curl_close($ch);

$responseData = json_decode($response, true);

if (isset($responseData['choices'][0]['message']['content'])) {
    echo json_encode(['reply' => $responseData['choices'][0]['message']['content']]);
} else {
    $errorMsg = $responseData['error']['message'] ?? 'No reply from AI.';
    echo json_encode(['error' => $errorMsg]);
}
?>
