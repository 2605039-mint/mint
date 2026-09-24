<?php
// index.php - Home page of Mint Cake Shop
$page_title = "Artisan Cakes & Sweet Delights";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Fetch featured cakes
$featured_stmt = $db->query("
    SELECT c.*, cat.name as category_name 
    FROM cakes c 
    JOIN categories cat ON c.category_id = cat.id 
    WHERE c.is_featured = 1 AND c.is_available = 1 
    ORDER BY c.id ASC 
    LIMIT 6
");
$featured_cakes = $featured_stmt->fetchAll();

// Fetch all categories
$cat_stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $cat_stmt->fetchAll();
?>

<!-- Flash Messages (e.g. item added to cart) -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-medium"><?= h($_SESSION['flash_success']) ?></span>
            </div>
            <a href="cart.php" class="text-sm font-semibold underline text-emerald-900 hover:text-emerald-700">View Cart &rarr;</a>
        </div>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-mint-100/60 via-rosecream-50 to-rosecream-50 py-16 sm:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 bg-white px-3 py-1.5 rounded-full shadow-sm border border-mint-200 text-xs font-semibold text-mint-800 uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-mint-500 animate-pulse"></span>
                    <span>Handmade Daily with Love</span>
                </div>
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 tracking-tight leading-[1.15]">
                    Delicious Cakes Made for Life's Sweetest Moments.
                </h1>
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    From velvety Japanese sponge cakes to decadent Belgian chocolate mousses, our pastry chefs handcraft every cake fresh every single morning.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="menu.php" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-mint-600 hover:bg-mint-700 text-white font-semibold shadow-lg shadow-mint-500/25 transition-all transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-cake-candles mr-2"></i> Explore Cake Menu
                    </a>
                    <a href="#about" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-white hover:bg-gray-50 text-gray-800 font-semibold border border-gray-200 shadow-sm transition-all">
                        Our Story
                    </a>
                </div>
                <!-- Trust badges -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-gray-200/60 text-center lg:text-left">
                    <div>
                        <div class="font-bold text-gray-900 text-xl font-serif">100%</div>
                        <div class="text-xs text-gray-500">Pure Butter & Cream</div>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-xl font-serif">Daily</div>
                        <div class="text-xs text-gray-500">Fresh Oven Bakes</div>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-xl font-serif">4.9 ★</div>
                        <div class="text-xs text-gray-500">Over 1,200 Reviews</div>
                    </div>
                </div>
            </div>

            <!-- Hero Image Showcase -->
            <div class="relative flex justify-center">
                <div class="relative w-full max-w-lg">
                    <!-- Decorative backdrops -->
                    <div class="absolute -top-4 -left-4 w-72 h-72 bg-mint-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob"></div>
                    <div class="absolute -bottom-8 right-0 w-72 h-72 bg-rose-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>

                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=1000&q=80" 
                             alt="Signature Mint Chocolate Drip Cake" 
                             class="w-full h-96 sm:h-[450px] object-cover hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-mint-600 uppercase tracking-wider">Chef's Signature</span>
                                <h4 class="font-serif font-bold text-gray-900 text-base">Mint Choco Drip Cake</h4>
                            </div>
                            <span class="text-lg font-bold text-gray-900">$38.50</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cake Categories Section -->
<section class="py-14 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs font-bold text-mint-600 uppercase tracking-widest">Browse by Category</span>
            <h2 class="font-serif text-3xl font-bold text-gray-900 mt-1">Handmade For Every Craving</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
            <?php foreach ($categories as $cat): ?>
                <a href="menu.php?category=<?= h($cat['slug']) ?>" 
                   class="group bg-rosecream-50 hover:bg-mint-50 p-6 rounded-2xl text-center border border-gray-100 hover:border-mint-200 transition-all duration-300 shadow-sm hover:shadow-md flex flex-col items-center">
                    <div class="w-14 h-14 rounded-full bg-white group-hover:bg-mint-500 text-mint-600 group-hover:text-white flex items-center justify-center text-2xl mb-3 shadow-sm transition-colors duration-300">
                        <i class="fa-solid <?= h($cat['icon'] ?? 'fa-cake-candles') ?>"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm group-hover:text-mint-800 transition-colors">
                        <?= h($cat['name']) ?>
                    </h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Cakes Section -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-12">
            <div>
                <span class="text-xs font-bold text-mint-600 uppercase tracking-widest">Customer Favorites</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 mt-1">Featured Creations</h2>
            </div>
            <a href="menu.php" class="mt-4 sm:mt-0 inline-flex items-center text-sm font-semibold text-mint-700 hover:text-mint-800">
                View All Menu Cakes <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_cakes as $cake): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 flex flex-col group">
                    <!-- Image -->
                    <div class="relative overflow-hidden aspect-[4/3] bg-gray-100">
                        <img src="<?= h($cake['image_url']) ?>" 
                             alt="<?= h($cake['name']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-semibold px-2.5 py-1 rounded-full text-gray-700 shadow-sm">
                            <?= h($cake['category_name']) ?>
                        </span>
                        <?php if ($cake['is_featured']): ?>
                            <span class="absolute top-3 right-3 bg-mint-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                                Popular
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Details -->
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
                            <span class="font-serif text-2xl font-bold text-gray-900">
                                <?= format_price($cake['price']) ?>
                            </span>

                            <div class="flex items-center space-x-2">
                                <a href="cake.php?id=<?= $cake['id'] ?>" class="text-xs font-semibold px-3 py-2 rounded-xl text-gray-600 bg-gray-50 hover:bg-gray-100 transition">
                                    Details
                                </a>
                                <form action="cart.php" method="POST" class="inline">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="cake_id" value="<?= $cake['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-mint-600 hover:bg-mint-700 text-white shadow-md transition-colors" title="Add to bag">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About / Story Section -->
<section id="about" class="py-20 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=600&q=80" alt="Baking pastry" class="rounded-2xl shadow-md object-cover h-64 w-full">
                    <img src="https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=600&q=80" alt="Cheesecake" class="rounded-2xl shadow-md object-cover h-64 w-full mt-6">
                </div>
            </div>

            <div class="space-y-6 order-1 lg:order-2">
                <span class="text-xs font-bold text-mint-600 uppercase tracking-widest">Our Philosophy</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
                    Pure Ingredients, French Precision, Modern Flavor.
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    At Mint Patisserie, we believe that true indulgence comes from balance. We never use artificial preservatives, hydrogenated oils, or artificial flavorings.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Every batch uses grass-fed creamery butter, single-origin Valrhona chocolate, fresh seasonal berries, and Madagascar bourbon vanilla. Whether it is an intimate birthday or a grand celebration, our goal is to deliver an unforgettable sweet experience.
                </p>
                <div class="grid grid-cols-2 gap-6 pt-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-mint-100 text-mint-700 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-wheat-awn"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Finest Flours</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Imported Japanese sponge flours for ultra-airy softness.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-mint-100 text-mint-700 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Baked on Day</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Assembled and finished hours before your pickup.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action -->
<section class="py-16 bg-gradient-to-r from-mint-700 to-mint-800 text-white text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold">Have a Special Occasion Coming Up?</h2>
        <p class="text-mint-100 text-base max-w-2xl mx-auto leading-relaxed">
            Order online today for store pickup or local courier delivery. Add custom piping messages for birthdays, anniversaries, and graduations at checkout!
        </p>
        <div class="pt-2">
            <a href="menu.php" class="inline-flex items-center px-8 py-3.5 rounded-xl bg-white text-mint-800 font-bold hover:bg-mint-50 shadow-lg transition">
                <i class="fa-solid fa-bag-shopping mr-2"></i> Order Your Cake Now
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
