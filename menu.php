<?php
// menu.php - Full Cake Catalog & Ordering Menu
$page_title = "Our Cake Menu";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Filter parameters
$selected_category_slug = $_GET['category'] ?? 'all';
$search_query = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'popular';

// Fetch all categories for filter buttons
$cat_stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $cat_stmt->fetchAll();

// Build SQL query
$sql = "
    SELECT c.*, cat.name as category_name, cat.slug as category_slug 
    FROM cakes c 
    JOIN categories cat ON c.category_id = cat.id 
    WHERE 1=1
";
$params = [];

if ($selected_category_slug !== 'all' && !empty($selected_category_slug)) {
    $sql .= " AND cat.slug = ?";
    $params[] = $selected_category_slug;
}

if (!empty($search_query)) {
    $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
}

// Sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY c.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY c.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY c.name ASC";
        break;
    case 'popular':
    default:
        $sql .= " ORDER BY c.is_featured DESC, c.id ASC";
        break;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$cakes = $stmt->fetchAll();
?>

<!-- Header Banner -->
<section class="bg-mint-100/50 py-12 border-b border-mint-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold text-mint-700 uppercase tracking-widest">Handcrafted Patisserie</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Our Cake Collection</h1>
        <p class="text-gray-600 text-sm sm:text-base max-w-xl mx-auto mt-2">
            Each creation is baked with whole fruits, organic dairy, and European couvertures. Pick your favorites for pickup or courier delivery.
        </p>
    </div>
</section>

<!-- Filter & Search Controls -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <form action="menu.php" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search Bar -->
            <div class="relative w-full md:w-80">
                <input type="text" name="q" value="<?= h($search_query) ?>" placeholder="Search cakes, berries, chocolate..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-mint-500 text-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
            </div>

            <!-- Sort Dropdown -->
            <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                <input type="hidden" name="category" value="<?= h($selected_category_slug) ?>">
                <label for="sortSelect" class="text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Sort By:</label>
                <select id="sortSelect" name="sort" onchange="this.form.submit()" 
                        class="py-2.5 px-3 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 font-medium">
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popular & Featured</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name: A to Z</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-mint-600 hover:bg-mint-700 text-white rounded-xl text-sm font-semibold transition">
                    Filter
                </button>
            </div>
        </form>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap gap-2 pt-5 mt-4 border-t border-gray-100">
            <a href="menu.php?category=all<?= !empty($search_query) ? '&q=' . urlencode($search_query) : '' ?>&sort=<?= $sort ?>" 
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors <?= $selected_category_slug === 'all' ? 'bg-mint-700 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                All Cakes
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="menu.php?category=<?= h($cat['slug']) ?><?= !empty($search_query) ? '&q=' . urlencode($search_query) : '' ?>&sort=<?= $sort ?>" 
                   class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors <?= $selected_category_slug === $cat['slug'] ? 'bg-mint-700 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    <?= h($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Product Results Section -->
    <div class="flex items-center justify-between mb-6">
        <span class="text-sm font-medium text-gray-500">
            Showing <strong class="text-gray-900"><?= count($cakes) ?></strong> <?= count($cakes) === 1 ? 'cake' : 'cakes' ?>
        </span>
        <?php if ($selected_category_slug !== 'all' || !empty($search_query)): ?>
            <a href="menu.php" class="text-xs font-semibold text-mint-700 hover:underline">
                <i class="fa-solid fa-xmark mr-1"></i> Clear all filters
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($cakes)): ?>
        <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 p-8">
            <div class="w-16 h-16 rounded-full bg-rosecream-100 text-rosecream-500 mx-auto flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-cookie"></i>
            </div>
            <h3 class="font-serif text-xl font-bold text-gray-900 mb-1">No cakes found</h3>
            <p class="text-sm text-gray-500 mb-6">We couldn't find any cakes matching your search criteria.</p>
            <a href="menu.php" class="px-6 py-2.5 rounded-xl bg-mint-600 text-white text-sm font-semibold hover:bg-mint-700 transition">
                Reset Menu Filters
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($cakes as $cake): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 flex flex-col group">
                    <!-- Image -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="<?= h($cake['image_url']) ?>" 
                             alt="<?= h($cake['name']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm text-xs font-semibold px-2.5 py-1 rounded-full text-gray-700 shadow-sm">
                            <?= h($cake['category_name']) ?>
                        </span>
                        <?php if ($cake['is_featured']): ?>
                            <span class="absolute top-3 right-3 bg-mint-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                                Chef's Pick
                            </span>
                        <?php endif; ?>
                        <?php if (!$cake['is_available']): ?>
                            <div class="absolute inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center">
                                <span class="bg-rose-600 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider">
                                    Sold Out Today
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Body -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-serif text-xl font-bold text-gray-900 mb-2 group-hover:text-mint-700 transition-colors">
                                <a href="cake.php?id=<?= $cake['id'] ?>"><?= h($cake['name']) ?></a>
                            </h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                                <?= h($cake['description']) ?>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-400 block font-medium">Standard 6-inch</span>
                                <span class="font-serif text-2xl font-bold text-gray-900">
                                    <?= format_price($cake['price']) ?>
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <a href="cake.php?id=<?= $cake['id'] ?>" class="text-xs font-semibold px-3 py-2 rounded-xl text-gray-600 bg-gray-50 hover:bg-gray-100 transition">
                                    View
                                </a>
                                <?php if ($cake['is_available']): ?>
                                    <form action="cart.php" method="POST" class="inline">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="cake_id" value="<?= $cake['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl bg-mint-600 hover:bg-mint-700 text-white text-xs font-semibold shadow-md transition-colors">
                                            <i class="fa-solid fa-plus mr-1"></i> Add
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
