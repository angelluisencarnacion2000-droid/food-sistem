<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> - Food System</title>
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
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Información del restaurante -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">
                            <?= htmlspecialchars($restaurant['name']) ?>
                        </h1>
                        <p class="text-gray-500 mt-2"><?= htmlspecialchars($restaurant['category']) ?></p>
                        <p class="text-gray-600 mt-2"><?= htmlspecialchars($restaurant['description']) ?></p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center justify-end">
                            <span class="text-yellow-400 text-2xl">★</span>
                            <span class="ml-1 text-xl"><?= number_format($restaurant['rating'], 1) ?></span>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <p><?= $restaurant['is_open'] ? 'Abierto' : 'Cerrado' ?></p>
                            <p>Tiempo de entrega: <?= $restaurant['delivery_time'] ?> min</p>
                            <p>Pedido mínimo: $<?= number_format($restaurant['minimum_order'], 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menú del restaurante -->
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Menú</h2>

                <!-- Categorías del menú -->
                <?php foreach ($menuCategories as $category => $items): ?>
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4"><?= htmlspecialchars($category) ?></h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($items as $item): ?>
                                <div class="bg-white rounded-lg shadow-md p-4 border">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-800">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </h4>
                                            <p class="text-gray-600 text-sm mt-1">
                                                <?= htmlspecialchars($item['description']) ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-semibold text-gray-800">
                                                $<?= number_format($item['price'], 2) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if ($restaurant['is_open']): ?>
                                        <button 
                                            onclick="addToCart(<?= $item['id'] ?>)"
                                            class="mt-4 w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                            Agregar al Carrito
                                        </button>
                                    <?php else: ?>
                                        <button 
                                            disabled
                                            class="mt-4 w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed">
                                            Restaurante Cerrado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Carrito flotante -->
    <div id="cart" class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 border" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Carrito de Compras</h3>
        <div id="cart-items" class="max-h-60 overflow-y-auto"></div>
        <div class="mt-4 border-t pt-4">
            <div class="flex justify-between items-center mb-4">
                <span class="font-semibold">Total:</span>
                <span id="cart-total" class="font-semibold">$0.00</span>
            </div>
            <button 
                onclick="checkout()"
                class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                Realizar Pedido
            </button>
        </div>
    </div>

    <script>
        let cart = [];
        
        function addToCart(itemId) {
            // Lógica para agregar al carrito
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartElement = document.getElementById('cart');
            if (cart.length > 0) {
                cartElement.style.display = 'block';
                // Actualizar contenido del carrito
            } else {
                cartElement.style.display = 'none';
            }
        }

        function checkout() {
            // Lógica para procesar el pedido
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