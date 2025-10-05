<?php
require_once __DIR__ . '/../app/controllers/ChatbotController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['message'])) {
        $chatbot = new ChatbotController();
        echo $chatbot->processMessage($data['message']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No message provided']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}