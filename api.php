<?php
require 'vendor/autoload.php'; // Asegúrate de que esta línea apunta correctamente a tu autoload.php

use OpenAI\Client;

// Configura tu clave API de OpenAI
$apiKey = 'YOUR_OPENAI_API_KEY';

// Crea una instancia del cliente de OpenAI
$client = new Client($apiKey);

// Obtén el contenido de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['question'])) {
    echo json_encode(['error' => 'No question provided']);
    exit;
}

$question = $data['question'];

// Realiza la llamada a la API de OpenAI
try {
    $response = $client->completions()->create([
        'model' => 'text-davinci-003',
        'prompt' => $question,
        'max_tokens' => 150,
    ]);

    $answer = $response['choices'][0]['text'];

    echo json_encode(['answer' => trim($answer)]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
