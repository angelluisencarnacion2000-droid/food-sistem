<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Food System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <a href="/" class="flex items-center">
                        <span class="text-xl font-bold text-gray-800">🍽️ Food System</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/orders" class="text-gray-600 hover:text-gray-900 px-3 py-2">Mis Pedidos</a>
                        <form action="/logout" method="POST" class="ml-4">
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Cerrar Sesión</button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="text-gray-600 hover:text-gray-900 px-3 py-2">Iniciar Sesión</a>
                        <a href="/register" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Carrito de Compras</h1>

            <?php if (empty($cartItems)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">Tu carrito está vacío</p>
                    <a href="/" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
                        Explorar Restaurantes
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="flex items-center justify-between border-b pb-4">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </h3>
                                    <p class="text-gray-500 text-sm">
                                        <?= htmlspecialchars($item['restaurant_name']) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <button 
                                        onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')"
                                        class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md hover:bg-gray-300">
                                        -
                                    </button>
                                    <span class="text-gray-800 font-semibold"><?= $item['quantity'] ?></span>
                                    <button 
                                        onclick="updateQuantity(<?= $item['id'] ?>, 'increase')"
                                        class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md hover:bg-gray-300">
                                        +
                                    </button>
                                </div>

                                <span class="text-gray-800 font-semibold">
                                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </span>

                                <button 
                                    onclick="removeItem(<?= $item['id'] ?>)"
                                    class="text-red-500 hover:text-red-600">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Resumen del pedido -->
                    <div class="mt-8 border-t pt-6">
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Costo de envío:</span>
                                <span>$<?= number_format($deliveryCost, 2) ?></span>
                            </div>
                            <div class="flex justify-between font-semibold text-lg text-gray-800 border-t pt-2">
                                <span>Total:</span>
                                <span>$<?= number_format($total, 2) ?></span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <button 
                                onclick="checkout()"
                                class="w-full bg-green-500 text-white px-6 py-3 rounded-md hover:bg-green-600 font-semibold">
                                Proceder al Pago
                            </button>
                            <a 
                                href="/"
                                class="block w-full text-center text-gray-600 hover:text-gray-800">
                                Continuar Comprando
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateQuantity(itemId, action) {
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    itemId: itemId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function removeItem(itemId) {
            if (confirm('¿Estás seguro de que quieres eliminar este artículo?')) {
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        itemId: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        }

        function checkout() {
            window.location.href = '/checkout';
        }

        // Inicializar chatbot
        const chatbot = {
            init() {
                // Implementación del chatbot aquí
            }
        };

        chatbot.init();
    </script>
</body>
</html>