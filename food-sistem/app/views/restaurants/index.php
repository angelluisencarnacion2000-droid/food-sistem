<?php
$categories = ['Todos', 'Pizza', 'Hamburguesas', 'Sushi', 'Mexicana', 'Italiana', 'China', 'Postres'];

// Filtrar restaurantes según búsqueda y categoría
$search = $_GET['search'] ?? '';
$selectedCategory = $_GET['category'] ?? 'Todos';

$filteredRestaurants = array_filter($restaurants, function($restaurant) use ($search, $selectedCategory) {
    $matchesSearch = empty($search) || 
                    stripos($restaurant['name'], $search) !== false || 
                    stripos($restaurant['description'], $search) !== false;
    
    $matchesCategory = $selectedCategory === 'Todos' || 
                      $restaurant['category'] === $selectedCategory;
    
    return $matchesSearch && $matchesCategory;
});
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Location Header -->
        <div class="flex items-center text-orange-500 mb-4">
            <i class="fas fa-map-marker-alt mr-2"></i>
            <span>Entregando en Madrid, España</span>
        </div>

        <h2 class="text-3xl font-bold text-gray-900 mb-2">Restaurantes Cerca de Ti</h2>
        <p class="text-gray-600 mb-6">Descubre los sabores locales de tu ciudad</p>

        <!-- Search and Filters -->
        <div class="mb-8 space-y-4">
            <form action="/restaurants" method="GET" class="space-y-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Buscar restaurantes o comida..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"
                    >
                </div>

                <div class="flex items-center space-x-4 overflow-x-auto pb-2">
                    <div class="flex items-center text-gray-700 font-medium whitespace-nowrap">
                        <i class="fas fa-filter mr-2"></i>
                        Filtros:
                    </div>
                    <?php foreach ($categories as $category): ?>
                        <button 
                            type="submit" 
                            name="category" 
                            value="<?php echo $category; ?>"
                            class="<?php echo $category === $selectedCategory 
                                ? 'bg-orange-500 text-white' 
                                : 'bg-white text-gray-600 hover:bg-orange-50 border border-gray-200'; ?> 
                                px-4 py-2 rounded-full whitespace-nowrap transition-colors">
                            <?php echo $category; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>

        <!-- Restaurant Grid -->
        <?php if (empty($filteredRestaurants)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🍽️</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    No se encontraron restaurantes
                </h3>
                <p class="text-gray-600">
                    Intenta cambiar los filtros o buscar algo diferente
                </p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($filteredRestaurants as $restaurant): ?>
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2 overflow-hidden group">
                        <div class="relative">
                            <img
                                src="<?php echo htmlspecialchars($restaurant['image']); ?>"
                                alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                                class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
                            >
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php 
                                    echo $restaurant['is_open'] 
                                        ? 'bg-green-500 text-white' 
                                        : 'bg-red-500 text-white'; 
                                    ?>">
                                    <?php echo $restaurant['is_open'] ? 'Abierto' : 'Cerrado'; ?>
                                </span>
                            </div>
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-black bg-opacity-70 text-white px-2 py-1 rounded text-sm">
                                    <?php echo htmlspecialchars($restaurant['category']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                                <?php echo htmlspecialchars($restaurant['name']); ?>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($restaurant['description']); ?>
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="font-medium"><?php echo number_format($restaurant['rating'], 1); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-1"></i>
                                    <span><?php echo htmlspecialchars($restaurant['delivery_time']); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-truck mr-1"></i>
                                    <span>$<?php echo number_format($restaurant['delivery_fee'], 2); ?></span>
                                </div>
                            </div>

                            <a href="/restaurant/<?php echo $restaurant['id']; ?>" 
                               class="block w-full text-center bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors font-medium">
                                Ver Menú
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar la navegación del scroll horizontal en los filtros
    const filterContainer = document.querySelector('.overflow-x-auto');
    let isDown = false;
    let startX;
    let scrollLeft;

    filterContainer.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - filterContainer.offsetLeft;
        scrollLeft = filterContainer.scrollLeft;
    });

    filterContainer.addEventListener('mouseleave', () => {
        isDown = false;
    });

    filterContainer.addEventListener('mouseup', () => {
        isDown = false;
    });

    filterContainer.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - filterContainer.offsetLeft;
        const walk = (x - startX) * 2;
        filterContainer.scrollLeft = scrollLeft - walk;
    });
});