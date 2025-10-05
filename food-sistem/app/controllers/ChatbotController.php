<?php

class ChatbotController {
    private $api_key;
    private $model = "gpt-3.5-turbo";
    private $max_tokens = 150;
    private $db;

    public function __construct() {
        // En producción, esto debería estar en un archivo de configuración seguro
        $this->api_key = getenv('OPENAI_API_KEY');
        
        // Conexión a la base de datos para el historial de chat
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getInstance()->getConnection();
        
        // Crear tabla de historial si no existe
        $this->createHistoryTable();
    }

    private function createHistoryTable() {
        $sql = "CREATE TABLE IF NOT EXISTS chat_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            session_id VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id),
            INDEX (session_id)
        )";
        $this->db->exec($sql);
    }

    public function processMessage() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['message'])) {
                throw new Exception('Mensaje no proporcionado');
            }

            $userMessage = $data['message'];
            $sessionId = session_id();
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            // Obtener historial reciente para contexto
            $history = $this->getRecentHistory($sessionId);
            
            // Preparar el contexto del restaurante
            $context = "Eres un asistente amigable y experto en un sistema de pedidos de comida llamado Food System. 
                      Tu objetivo es proporcionar la mejor experiencia al usuario ayudándole con:
                      
                      1. Recomendaciones personalizadas de restaurantes y platos
                      2. Información detallada sobre menús, precios y tiempos de entrega
                      3. Ayuda con el proceso de pedido y pago
                      4. Estado de pedidos y seguimiento
                      5. Resolución de problemas y dudas frecuentes
                      
                      Características importantes:
                      - Horario de entrega: 11:00 AM - 11:00 PM
                      - Área de cobertura: Madrid ciudad y alrededores
                      - Tiempo promedio de entrega: 30-45 minutos
                      - Métodos de pago: Tarjeta, PayPal y efectivo
                      
                      Mantén un tono amigable y profesional. Da respuestas concisas pero útiles.
                      Usa emojis ocasionalmente para hacer la conversación más amena. 
                      Si no estás seguro de algo, ofrece alternativas o sugiere contactar con atención al cliente.
                      
                      Historial de la conversación:";

            // Agregar historial al contexto
            foreach ($history as $chat) {
                $context .= "\nUsuario: {$chat['message']}\nAsistente: {$chat['response']}\n";
            }

            // Preparar la conversación para OpenAI
            $conversation = [
                ["role" => "system", "content" => $context],
                ["role" => "user", "content" => $userMessage]
            ];

            // Realizar la petición a OpenAI
            $response = $this->callOpenAI($conversation);

            // Procesar la respuesta
            $botResponse = isset($response['choices'][0]['message']['content']) 
                ? $response['choices'][0]['message']['content']
                : 'Lo siento, no pude procesar tu mensaje en este momento.';

            // Guardar en el historial
            $this->saveToHistory($sessionId, $userId, $userMessage, $botResponse);

            // Enviar respuesta
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'response' => $botResponse,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getRecentHistory($sessionId) {
        $stmt = $this->db->prepare(
            "SELECT message, response FROM chat_history 
             WHERE session_id = ? 
             ORDER BY created_at DESC 
             LIMIT 5"
        );
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function saveToHistory($sessionId, $userId, $message, $response) {
        $stmt = $this->db->prepare(
            "INSERT INTO chat_history (session_id, user_id, message, response) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$sessionId, $userId, $message, $response]);
    }

    private function callOpenAI($conversation) {
        if (empty($this->api_key)) {
            // Fallback a respuestas predefinidas si no hay API key
            return [
                'choices' => [[
                    'message' => [
                        'content' => $this->getFallbackResponse($conversation[1]['content'])
                    ]
                ]]
            ];
        }

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);
        
        $data = [
            'model' => $this->model,
            'messages' => $conversation,
            'max_tokens' => $this->max_tokens,
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0.2,
            'presence_penalty' => 0.2
        ];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Error en la petición a OpenAI: ' . curl_error($ch));
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }

    private function getFallbackResponse($message) {
        $keywords = [
            'hola' => '¡Hola! 👋 ¿En qué puedo ayudarte hoy?',
            'menu' => 'Puedes encontrar los menús completos en las páginas de cada restaurante. ¿Te gustaría que te ayude a encontrar algo específico? 🍽️',
            'pedido' => 'Para realizar un pedido, selecciona los platos que desees y agrégalos al carrito. ¿Necesitas ayuda con algún paso? 🛒',
            'precio' => 'Los precios varían según el restaurante y el plato. ¿Buscas algo en particular? 💰',
            'horario' => 'Nuestro horario de entrega es de 11:00 AM a 11:00 PM. ¿Necesitas hacer un pedido? 🕒',
            'gracias' => '¡De nada! Estoy aquí para ayudarte. ¿Hay algo más en lo que pueda asistirte? 😊',
            'adios' => '¡Hasta luego! Que tengas un excelente día. ¡Vuelve pronto! 👋'
        ];

        foreach ($keywords as $key => $response) {
            if (stripos($message, $key) !== false) {
                return $response;
            }
        }

        return "Estoy aquí para ayudarte con información sobre restaurantes, menús, pedidos y más. ¿Podrías ser más específico con tu pregunta? 🤔";
    }
}