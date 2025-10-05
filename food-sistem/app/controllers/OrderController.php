<?php

class OrderController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        global $currentPage;
        $currentPage = 'orders';

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = 'Debes iniciar sesión para ver tus pedidos';
            header('Location: /');
            exit;
        }

        $stmt = $this->db->prepare("
            SELECT o.*, r.name as restaurant_name
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $stmt = $this->db->prepare("
                SELECT oi.*, mi.name, mi.price
                FROM order_items oi
                JOIN menu_items mi ON oi.menu_item_id = mi.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order['id']]);
            $order['items'] = $stmt->fetchAll();
        }

        require __DIR__ . '/../views/orders/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Debes iniciar sesión para realizar un pedido']);
            exit;
        }

        if (empty($_SESSION['cart'])) {
            echo json_encode(['error' => 'El carrito está vacío']);
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Obtener información del carrito
            $menuItemIds = array_column($_SESSION['cart'], 'menu_item_id');
            $placeholders = str_repeat('?,', count($menuItemIds) - 1) . '?';
            
            $stmt = $this->db->prepare("
                SELECT mi.*, r.id as restaurant_id, r.delivery_fee
                FROM menu_items mi
                JOIN restaurants r ON mi.restaurant_id = r.id
                WHERE mi.id IN ($placeholders)
            ");
            $stmt->execute($menuItemIds);
            $items = $stmt->fetchAll();

            // Calcular total
            $total = 0;
            $restaurantId = $items[0]['restaurant_id'];
            $deliveryFee = $items[0]['delivery_fee'];

            foreach ($items as $item) {
                $quantity = $_SESSION['cart'][array_search($item['id'], $menuItemIds)]['quantity'];
                $total += $item['price'] * $quantity;
            }

            $total += $deliveryFee;

            // Crear el pedido
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, restaurant_id, total, status, payment_method, payment_status, delivery_address)
                VALUES (?, ?, ?, 'pending', ?, 'pending', ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $restaurantId,
                $total,
                $_POST['payment_method'],
                $_POST['delivery_address']
            ]);

            $orderId = $this->db->lastInsertId();

            // Insertar items del pedido
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $cartItem) {
                $item = $items[array_search($cartItem['menu_item_id'], array_column($items, 'id'))];
                $stmt->execute([
                    $orderId,
                    $cartItem['menu_item_id'],
                    $cartItem['quantity'],
                    $item['price']
                ]);
            }

            $this->db->commit();

            // Limpiar carrito
            $_SESSION['cart'] = [];
            $_SESSION['flash'] = '¡Pedido realizado con éxito!';

            echo json_encode([
                'success' => true,
                'message' => 'Pedido creado correctamente',
                'orderId' => $orderId
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['error' => 'Error al procesar el pedido']);
        }
    }

    public function show($id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = 'Debes iniciar sesión para ver los detalles del pedido';
            header('Location: /');
            exit;
        }

        $stmt = $this->db->prepare("
            SELECT o.*, r.name as restaurant_name, r.phone as restaurant_phone
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $order = $stmt->fetch();

        if (!$order) {
            header('HTTP/1.0 404 Not Found');
            require __DIR__ . '/../views/404.php';
            return;
        }

        $stmt = $this->db->prepare("
            SELECT oi.*, mi.name, mi.price
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();

        require __DIR__ . '/../views/orders/show.php';
    }
}