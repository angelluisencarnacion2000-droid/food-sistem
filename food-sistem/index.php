<?php
session_start();

// Función para obtener la URL base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;
    if (dirname($_SERVER['SCRIPT_NAME']) !== '/') {
        $baseUrl .= dirname($_SERVER['SCRIPT_NAME']);
    }
    return rtrim($baseUrl, '/');
}

// Función para servir archivos estáticos
function serveStaticFile($path) {
    $filePath = __DIR__ . '/public' . $path;
    if (file_exists($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon'
        ];
        
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
            readfile($filePath);
            exit;
        }
    }
    return false;
}

// Función para enrutar las peticiones
function route($path) {
    // Comprobar si es un archivo estático
    if (strpos($path, '/static/') === 0) {
        if (serveStaticFile($path)) {
            return;
        }
    }

    $routes = [
        '/' => ['HomeController', 'index'],
        '/restaurants' => ['RestaurantController', 'index'],
        '/restaurant/{id}' => ['RestaurantController', 'show'],
        '/restaurant/{id}/menu' => ['RestaurantController', 'menu'],
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/cart' => ['CartController', 'index'],
        '/orders' => ['OrderController', 'index'],
        '/profile' => ['UserController', 'profile'],
        '/chatbot' => ['ChatbotController', 'processMessage']
    ];

    // Remover query string si existe
    $path = strtok($path, '?');

    // Remover trailing slash
    $path = rtrim($path, '/');

    // Si la ruta está vacía, redirigir a home
    if ($path === '') {
        $path = '/';
    }

    // Buscar una coincidencia en las rutas
    foreach ($routes as $route => $handler) {
        $pattern = preg_replace('/{[^}]+}/', '([^/]+)', $route);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches); // Remover la coincidencia completa
            
            // Cargar el controlador
            require_once __DIR__ . '/app/controllers/' . $handler[0] . '.php';
            
            // Instanciar el controlador
            $controller = new $handler[0]();
            
            // Llamar al método con los parámetros capturados
            return call_user_func_array([$controller, $handler[1]], $matches);
        }
    }

    // Si no se encuentra la ruta, mostrar error 404
    header("HTTP/1.0 404 Not Found");
    require __DIR__ . '/app/views/404.php';
}

// Configuración global
$baseUrl = getBaseUrl();
define('BASE_URL', $baseUrl);

// Obtener la ruta actual
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = substr($path, strlen(dirname($_SERVER['SCRIPT_NAME'])));

// Servir archivos estáticos si existen
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicPath = __DIR__ . '/public' . $requestPath;

if (file_exists($publicPath) && is_file($publicPath)) {
    // Establecer el tipo MIME correcto
    $extension = pathinfo($publicPath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
    ];
    
    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }
    
    readfile($publicPath);
    exit;
}

// Enrutar la petición
route($path);