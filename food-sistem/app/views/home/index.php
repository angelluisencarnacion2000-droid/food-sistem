<?php
$features = [
    [
        'icon' => '<i class="fas fa-truck text-2xl"></i>',
        'title' => 'Entrega Rápida',
        'description' => 'Recibe tu comida en 30-45 minutos'
    ],
    [
        'icon' => '<i class="fas fa-shield-alt text-2xl"></i>',
        'title' => 'Pago Seguro',
        'description' => 'Transacciones 100% seguras y protegidas'
    ],
    [
        'icon' => '<i class="fas fa-clock text-2xl"></i>',
        'title' => 'Disponible 24/7',
        'description' => 'Ordena cuando quieras, estamos siempre aquí'
    ],
    [
        'icon' => '<i class="fas fa-star text-2xl"></i>',
        'title' => 'Calidad Garantizada',
        'description' => 'Solo los mejores restaurantes locales'
    ]
];

$stats = [
    ['number' => '500+', 'label' => 'Restaurantes'],
    ['number' => '10K+', 'label' => 'Pedidos Felices'],
    ['number' => '30min', 'label' => 'Entrega Promedio'],
    ['number' => '4.8★', 'label' => 'Calificación']
];

$categories = [
    ['name' => 'Pizza', 'emoji' => '🍕', 'bg' => 'bg-gradient-to-br from-yellow-400 to-orange-500'],
    ['name' => 'Hamburguesas', 'emoji' => '🍔', 'bg' => 'bg-gradient-to-br from-red-400 to-pink-500'],
    ['name' => 'Sushi', 'emoji' => '🍣', 'bg' => 'bg-gradient-to-br from-green-400 to-teal-500'],
    ['name' => 'Tacos', 'emoji' => '🌮', 'bg' => 'bg-gradient-to-br from-yellow-500 to-red-500'],
    ['name' => 'Ensaladas', 'emoji' => '🥗', 'bg' => 'bg-gradient-to-br from-green-500 to-emerald-600'],
    ['name' => 'Postres', 'emoji' => '🍰', 'bg' => 'bg-gradient-to-br from-pink-400 to-purple-500'],
    ['name' => 'Pollo', 'emoji' => '🍗', 'bg' => 'bg-gradient-to-br from-orange-400 to-red-500'],
    ['name' => 'Pasta', 'emoji' => '🍝', 'bg' => 'bg-gradient-to-br from-yellow-600 to-orange-600']
];
?>

<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-food text-white">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        Tu comida
                        <span class="block text-yellow-300">favorita a</span>
                        <span class="block">domicilio</span>
                    </h1>
                    <p class="text-xl mb-8 text-orange-100">
                        Descubre los mejores restaurantes locales y disfruta de comida deliciosa 
                        entregada directamente a tu puerta en minutos.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/restaurants" class="btn-primary flex items-center justify-center">
                            Explorar Restaurantes
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <button class="btn-secondary flex items-center justify-center">
                            <i class="fas fa-phone mr-2"></i>
                            Llamar Ahora
                        </button>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="/img/hero-food.jpg" alt="Deliciosa comida" class="rounded-lg shadow-2xl w-full object-cover h-[500px]">
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16">
                <?php foreach ($stats as $stat): ?>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold mb-2"><?php echo $stat['number']; ?></div>
                        <div class="text-orange-100"><?php echo $stat['label']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    ¿Por qué elegir FoodLocal?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Ofrecemos la mejor experiencia de delivery con servicios confiables y comida de calidad
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($features as $feature): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2 text-center">
                        <div class="text-orange-500 mb-4">
                            <?php echo $feature['icon']; ?>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $feature['title']; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $feature['description']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-gray-900 text-center mb-12">
                Explora por Categoría
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($categories as $category): ?>
                    <a href="/restaurants?category=<?php echo urlencode($category['name']); ?>" 
                       class="<?php echo $category['bg']; ?> text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 text-center">
                        <div class="text-3xl mb-2"><?php echo $category['emoji']; ?></div>
                        <div class="font-semibold"><?php echo $category['name']; ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-orange text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">
                ¿Listo para tu próxima comida?
            </h2>
            <p class="text-xl mb-8 text-orange-100">
                Únete a miles de clientes satisfechos y descubre el sabor local
            </p>
            <a href="/restaurants" class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition-all transform hover:scale-105 inline-block">
                Empezar a Ordenar
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animaciones de entrada
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    });

    elements.forEach(el => observer.observe(el));
});
</script>