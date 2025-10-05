<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'FoodLocal - Tu comida favorita a domicilio'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/static/css/styles.css" rel="stylesheet">
    <link href="/static/css/restaurants.css" rel="stylesheet">
    <link href="/static/css/chatbot.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <!-- Logo y navegación izquierda -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center group">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white font-bold text-xl">🍕</span>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900 group-hover:text-orange-500 transition-colors">
                            FoodLocal
                        </span>
                    </a>
                    <div class="hidden md:flex ml-10 space-x-8">
                        <a href="/" class="nav-link <?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                            Inicio
                        </a>
                        <a href="/restaurants" class="nav-link <?php echo $currentPage === 'restaurants' ? 'active' : ''; ?>">
                            Restaurantes
                        </a>
                        <a href="/orders" class="nav-link <?php echo $currentPage === 'orders' ? 'active' : ''; ?>">
                            Mis Pedidos
                        </a>
                    </div>
                </div>

                <!-- Navegación derecha -->
                <div class="flex items-center space-x-4">
                    <!-- Ubicación -->
                    <div class="hidden md:flex items-center">
                        <span class="text-gray-600">📍</span>
                        <span class="ml-2 text-gray-600">Madrid, España</span>
                    </div>

                    <!-- Carrito -->
                    <button id="cartButton" class="text-gray-600 hover:text-gray-900 relative">
                        <span class="sr-only">Carrito</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="cartCount" class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?>
                        </span>
                    </button>

                    <!-- Botón de usuario -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="relative" id="userMenu">
                            <button class="flex items-center text-gray-700 hover:text-gray-900">
                                <img src="<?php echo $_SESSION['user']['avatar'] ?? '/img/default-avatar.png'; ?>" 
                                     alt="Avatar" 
                                     class="w-8 h-8 rounded-full">
                                <span class="ml-2 hidden md:block"><?php echo $_SESSION['user']['name']; ?></span>
                            </button>
                            <!-- Menú desplegable -->
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1" id="userDropdown">
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                                <a href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mis Pedidos</a>
                                <hr class="my-1">
                                <form action="/logout" method="POST" class="block">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <button id="loginButton" class="bg-orange-500 text-white px-6 py-2 rounded-full hover:bg-orange-600 transition duration-200">
                            Iniciar Sesión
                        </button>
                    <?php endif; ?>

                    <!-- Botón menú móvil -->
                    <button id="mobileMenuButton" class="md:hidden text-gray-600 hover:text-gray-900">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menú móvil -->
        <div class="hidden md:hidden" id="mobileMenu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/" class="mobile-nav-link <?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                    Inicio
                </a>
                <a href="/restaurants" class="mobile-nav-link <?php echo $currentPage === 'restaurants' ? 'active' : ''; ?>">
                    Restaurantes
                </a>
                <a href="/orders" class="mobile-nav-link <?php echo $currentPage === 'orders' ? 'active' : ''; ?>">
                    Mis Pedidos
                </a>
                <div class="flex items-center px-4 py-2 text-gray-600">
                    <span>📍</span>
                    <span class="ml-2">Madrid, España</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Modales -->
    <?php require_once __DIR__ . '/partials/auth-modal.php'; ?>
    <?php require_once __DIR__ . '/partials/cart-modal.php'; ?>
    <?php require_once __DIR__ . '/partials/chatbot.php'; ?>

    <!-- Scripts -->
    <script src="/js/main.js"></script>
    <script src="/js/auth.js"></script>
    <script src="/js/cart.js"></script>
    <script src="/js/chatbot.js"></script>
</body>
</html>