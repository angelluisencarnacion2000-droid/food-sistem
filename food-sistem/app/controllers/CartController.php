<?php

class CartController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getInstance()->getConnection();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index() {
        global $currentPage;
        $currentPage = 'cart';

        $cart = [];
        $total = 0;

        if (!empty($_SESSION['cart'])) {
            $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
            $menuItemIds = array_column($_SESSION['cart'], 'menu_item_id');
            
            $stmt = $this->db->prepare("
                SELECT mi.*, r.name as restaurant_name, r.delivery_fee
                FROM menu_items mi
                JOIN restaurants r ON mi.restaurant_id = r.id
                WHERE mi.id IN ($placeholders)
            ");
            $stmt->execute($menuItemIds);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $cartItem = $_SESSION['cart'][array_search($item['id'], $menuItemIds)];
                $cart[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $item['price'] * $cartItem['quantity'],
                    'restaurant_name' => $item['restaurant_name'],
                    'delivery_fee' => $item['delivery_fee']
                ];
                $total += $item['price'] * $cartItem['quantity'];
            }
        }

        require __DIR__ . '/../views/cart/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $menuItemId = $data['menu_item_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$menuItemId) {
            echo json_encode(['error' => 'ID del ítem no proporcionado']);
            exit;
        }

        // Verificar si el ítem existe
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$menuItemId]);
        $item = $stmt->fetch();

        if (!$item) {
            echo json_encode(['error' => 'Ítem no encontrado']);
            exit;
        }

        // Verificar si ya hay items de otro restaurante
        if (!empty($_SESSION['cart'])) {
            $firstItem = $_SESSION['cart'][0];
            $stmt = $this->db->prepare("
                SELECT restaurant_id 
                FROM menu_items 
                WHERE id IN (?, ?)
            ");
            $stmt->execute([$firstItem['menu_item_id'], $menuItemId]);
            $restaurants = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (count(array_unique($restaurants)) > 1) {
                echo json_encode([
                    'error' => 'Solo puedes pedir de un restaurante a la vez',
                    'needsClear' => true
                ]);
                exit;
            }
        }

        // Buscar el ítem en el carrito
        $index = array_search($menuItemId, array_column($_SESSION['cart'], 'menu_item_id'));

        if ($index !== false) {
            $_SESSION['cart'][$index]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][] = [
                'menu_item_id' => $menuItemId,
                'quantity' => $quantity
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Ítem agregado al carrito',
            'cartCount' => count($_SESSION['cart'])
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $menuItemId = $data['menu_item_id'] ?? null;
        $quantity = $data['quantity'] ?? 0;

        if (!$menuItemId) {
            echo json_encode(['error' => 'ID del ítem no proporcionado']);
            exit;
        }

        $index = array_search($menuItemId, array_column($_SESSION['cart'], 'menu_item_id'));

        if ($index === false) {
            echo json_encode(['error' => 'Ítem no encontrado en el carrito']);
            exit;
        }

        if ($quantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexar array
        }

        echo json_encode([
            'success' => true,
            'message' => 'Carrito actualizado',
            'cartCount' => count($_SESSION['cart'])
        ]);
    }

    public function clear() {
        $_SESSION['cart'] = [];
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode([
                'success' => true,
                'message' => 'Carrito vaciado'
            ]);
        } else {
            header('Location: /cart');
        }
    }
}