<?php
// cake.php - Single Cake Detail Page
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$cake_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("
    SELECT c.*, cat.name as category_name, cat.slug as category_slug 
    FROM cakes c 
    JOIN categories cat ON c.category_id = cat.id 
    WHERE c.id = ?
");
$stmt->execute([$cake_id]);
$cake = $stmt->fetch();

if (!$cake) {
    echo '<div class="max-w-4xl mx-auto px-4 py-20 text-center">
            <h2 class="font-serif text-3xl font-bold text-gray-900 mb-4">Cake Not Found</h2>
            <p class="text-gray-600 mb-8">The cake you are looking for might be out of season or unavailable.</p>
            <a href="menu.php" class="px-6 py-3 bg-mint-600 text-white font-semibold rounded-xl hover:bg-mint-700 transition">Return to Menu</a>
          </div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch related cakes from the same category
$rel_stmt = $db->prepare("
    SELECT c.*, cat.name as category_name 
    FROM cakes c 
    JOIN categories cat ON c.category_id = cat.id 
    WHERE c.category_id = ? AND c.id != ? 
    LIMIT 3
");
$rel_stmt->execute([$cake['category_id'], $cake['id']]);
$related_cakes = $rel_stmt->fetchAll();
?>

<!-- Breadcrumbs -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <nav class="flex items-center space-x-2 text-xs text-gray-500 font-medium">
        <a href="index.php" class="hover:text-mint-600">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="menu.php" class="hover:text-mint-600">Cake Menu</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="menu.php?category=<?= h($cake['category_slug']) ?>" class="hover:text-mint-600"><?= h($cake['category_name']) ?></a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-gray-900 truncate max-w-xs"><?= h($cake['name']) ?></span>
    </nav>
</div>

<!-- Main Cake Details -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Cake Image Display -->
        <div class="relative rounded-2xl overflow-hidden shadow-inner bg-gray-50 aspect-square max-h-[500px]">
            <img src="<?= h($cake['image_url']) ?>" 
                 alt="<?= h($cake['name']) ?>" 
                 class="w-full h-full object-cover">
            <?php if ($cake['is_featured']): ?>
                <span class="absolute top-4 left-4 bg-mint-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">
                    Chef's Favorite
                </span>
            <?php endif; ?>
        </div>

        <!-- Order & Information Details -->
        <div class="flex flex-col justify-between space-y-6">
            <div>
                <span class="inline-block bg-mint-50 text-mint-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    <?= h($cake['category_name']) ?>
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
                    <?= h($cake['name']) ?>
                </h1>
                
                <div class="mt-4 flex items-baseline space-x-3">
                    <span class="font-serif text-3xl font-bold text-gray-900">
                        <?= format_price($cake['price']) ?>
                    </span>
                    <span class="text-sm text-gray-500">Tax included</span>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-gray-600 text-base leading-relaxed">
                        <?= nl2br(h($cake['description'])) ?>
                    </p>
                </div>

                <!-- Specs & Features -->
                <div class="mt-6 grid grid-cols-2 gap-4 text-xs sm:text-sm bg-rosecream-50 p-4 rounded-2xl border border-rosecream-100">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-users text-mint-600 text-base"></i>
                        <span class="text-gray-700"><strong>Servings:</strong> 6 – 8 slices</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-ruler-combined text-mint-600 text-base"></i>
                        <span class="text-gray-700"><strong>Size:</strong> 15cm (6 inches)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-snowflake text-mint-600 text-base"></i>
                        <span class="text-gray-700"><strong>Storage:</strong> Keep cold (2–4°C)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-wheat-awn text-mint-600 text-base"></i>
                        <span class="text-gray-700"><strong>Allergens:</strong> Eggs, Milk, Wheat</span>
                    </div>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <div class="border-t border-gray-100 pt-6">
                <?php if ($cake['is_available']): ?>
                    <form action="cart.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="cake_id" value="<?= $cake['id'] ?>">

                        <div class="flex items-center space-x-4">
                            <!-- Quantity Selector -->
                            <div class="w-36">
                                <label for="quantity" class="block text-xs font-semibold text-gray-500 mb-1">Quantity</label>
                                <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden">
                                    <button type="button" onclick="changeQty(-1)" class="w-10 h-11 flex items-center justify-center text-gray-600 hover:bg-gray-200 text-sm font-bold transition">&minus;</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" 
                                           class="w-full text-center bg-transparent border-0 font-bold text-gray-900 focus:outline-none text-base">
                                    <button type="button" onclick="changeQty(1)" class="w-10 h-11 flex items-center justify-center text-gray-600 hover:bg-gray-200 text-sm font-bold transition">&plus;</button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex-grow pt-5">
                                <button type="submit" class="w-full h-11 px-6 bg-mint-600 hover:bg-mint-700 text-white font-semibold rounded-xl shadow-lg shadow-mint-600/25 flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Add to Cart &bull; <?= format_price($cake['price']) ?></span>
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="p-4 bg-gray-100 rounded-2xl text-center text-gray-600 font-semibold text-sm">
                        <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-500"></i> Currently Sold Out. Check back tomorrow!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Cakes -->
<?php if (!empty($related_cakes)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="font-serif text-2xl font-bold text-gray-900 mb-8">You Might Also Love</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <?php foreach ($related_cakes as $rel): ?>
            <a href="cake.php?id=<?= $rel['id'] ?>" class="group bg-white rounded-2xl p-4 shadow-sm hover:shadow-md border border-gray-100 flex items-center space-x-4 transition">
                <img src="<?= h($rel['image_url']) ?>" alt="<?= h($rel['name']) ?>" class="w-20 h-20 rounded-xl object-cover">
                <div>
                    <span class="text-[11px] font-bold text-mint-600 uppercase"><?= h($rel['category_name']) ?></span>
                    <h4 class="font-serif font-bold text-gray-900 text-sm group-hover:text-mint-700 transition"><?= h($rel['name']) ?></h4>
                    <span class="text-sm font-bold text-gray-800"><?= format_price($rel['price']) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
function changeQty(delta) {
    const input = document.getElementById('quantity');
    let val = parseInt(input.value) || 1;
    val = Math.max(1, Math.min(10, val + delta));
    input.value = val;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
