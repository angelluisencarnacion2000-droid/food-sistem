<?php
require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    private $model;

    public function __construct() {
        $this->model = new Restaurant();
    }

    public function index() {
        global $currentPage;
        $currentPage = 'restaurants';
        
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? 'Todos';
        
        $restaurants = $search || $category !== 'Todos'
            ? $this->model->search($search, $category)
            : $this->model->getAll();
            
        // Usamos categorías predefinidas en lugar de las de la base de datos
        $categories = ['Todos', 'Pizza', 'Hamburguesas', 'Sushi', 'Mexicana', 'Italiana', 'China', 'Postres'];
        
        // Aseguramos que tengamos todos los campos necesarios
        foreach ($restaurants as &$restaurant) {
            $restaurant['image'] = $restaurant['image'] ?? 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg';
            $restaurant['delivery_time'] = $restaurant['delivery_time'] ?? '30-45 min';
            $restaurant['delivery_fee'] = number_format($restaurant['delivery_fee'] ?? 2.99, 2);
            $restaurant['is_open'] = $restaurant['is_open'] ?? true;
        }
        
        // Agregar CSS personalizado
        $additionalCss = ['restaurants.css'];
        
        require __DIR__ . '/../views/restaurants/index.php';
    }

    public function show($id) {
        $restaurant = $this->model->find($id);
        if (!$restaurant) {
            header("HTTP/1.0 404 Not Found");
            require __DIR__ . '/../views/404.php';
            return;
        }
        
        require __DIR__ . '/../views/restaurants/show.php';
    }

    public function menu($id) {
        $restaurant = $this->model->find($id);
        if (!$restaurant) {
            header("HTTP/1.0 404 Not Found");
            require __DIR__ . '/../views/404.php';
            return;
        }

        $menuItems = $this->model->getMenu($id);
        require __DIR__ . '/../views/restaurants/menu.php';
    }
}