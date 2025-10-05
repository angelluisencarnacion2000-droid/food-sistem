<?php

class HomeController {
    public function index() {
        global $currentPage;
        $currentPage = 'home';

        $features = [
            [
                'icon' => 'fas fa-utensils',
                'title' => 'Variedad Culinaria',
                'description' => 'Explora una amplia selección de restaurantes locales con diferentes tipos de cocina.'
            ],
            [
                'icon' => 'fas fa-truck',
                'title' => 'Entrega Rápida',
                'description' => 'Entrega a domicilio rápida y confiable para que disfrutes tu comida caliente.'
            ],
            [
                'icon' => 'fas fa-star',
                'title' => 'Calidad Garantizada',
                'description' => 'Todos nuestros restaurantes son cuidadosamente seleccionados para garantizar la mejor calidad.'
            ],
            [
                'icon' => 'fas fa-mobile-alt',
                'title' => 'Pedidos Fáciles',
                'description' => 'Realiza pedidos de forma rápida y sencilla desde cualquier dispositivo.'
            ],
            [
                'icon' => 'fas fa-percent',
                'title' => 'Ofertas Especiales',
                'description' => 'Disfruta de descuentos exclusivos y promociones diarias en tus restaurantes favoritos.'
            ],
            [
                'icon' => 'fas fa-headset',
                'title' => 'Soporte 24/7',
                'description' => 'Nuestro equipo de soporte está disponible las 24 horas para ayudarte con cualquier consulta.'
            ]
        ];

        $popularRestaurants = [
            [
                'id' => 1,
                'name' => 'Pizza Italiana',
                'image' => 'https://images.pexels.com/photos/315755/pexels-photo-315755.jpeg',
                'category' => 'Pizza',
                'rating' => 4.8,
                'deliveryTime' => '25-35 min'
            ],
            [
                'id' => 2,
                'name' => 'Burger House',
                'image' => 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg',
                'category' => 'Hamburguesas',
                'rating' => 4.6,
                'deliveryTime' => '30-40 min'
            ],
            [
                'id' => 3,
                'name' => 'Sushi Zen',
                'image' => 'https://images.pexels.com/photos/357756/pexels-photo-357756.jpeg',
                'category' => 'Sushi',
                'rating' => 4.9,
                'deliveryTime' => '40-50 min'
            ]
        ];

        require_once __DIR__ . '/../views/home/index.php';
    }
}