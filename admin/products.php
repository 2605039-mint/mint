<?php
// admin/products.php - Cake Catalog & Inventory Management
$admin_title = "Cake Inventory";
require_once __DIR__ . '/header.php';

$db = get_db();

// Handle Cake Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $cake_id = (int)($_POST['cake_id'] ?? 0);
    if ($cake_id > 0) {
        $stmt = $db->prepare("DELETE FROM cakes WHERE id = ?");
        $stmt->execute([$cake_id]);
        $flash_msg = "Cake deleted successfully.";
    }
}

// Handle Quick Toggle Availability
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_availability') {
    $cake_id = (int)($_POST['cake_id'] ?? 0);
    $current = (int)($_POST['current_status'] ?? 0);
    $new_status = $current === 1 ? 0 : 1;

    $stmt = $db->prepare("UPDATE cakes SET is_available = ? WHERE id = ?");
    $stmt->execute([$new_status, $cake_id]);
    $flash_msg = "Availability updated.";
}

// Fetch all cakes
$stmt = $db->query("
    SELECT c.*, cat.name as category_name 
    FROM cakes c 
    JOIN categories cat ON c.category_id = cat.id 
    ORDER BY c.id DESC
");
$cakes = $stmt->fetchAll();
?>

<!-- Flash message -->
<?php if (isset($flash_msg)): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-2 text-sm">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <span><?= htmlspecialchars($flash_msg) ?></span>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold font-serif text-gray-900">Cake Inventory & Catalog</h1>
        <p class="text-sm text-gray-500 mt-1">Manage cakes, descriptions, prices, categories, and stock availability.</p>
    </div>
    <a href="product_form.php" class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition">
        <i class="fa-solid fa-plus mr-1.5"></i> Add New Cake
    </a>
</div>

<!-- Cake Inventory Table -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <span class="font-serif text-lg font-bold text-gray-900">Active Bakery Menu Items (<?= count($cakes) ?>)</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <tr>
                    <th class="py-3.5 px-6">Cake</th>
                    <th class="py-3.5 px-6">Category</th>
                    <th class="py-3.5 px-6">Price</th>
                    <th class="py-3.5 px-6">Featured</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($cakes as $cake): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <!-- Cake Info -->
                        <td class="py-4 px-6 flex items-center space-x-4">
                            <img src="<?= htmlspecialchars($cake['image_url']) ?>" alt="<?= htmlspecialchars($cake['name']) ?>" class="w-14 h-14 rounded-xl object-cover shadow-xs flex-shrink-0">
                            <div>
                                <h4 class="font-serif font-bold text-gray-900 text-base"><?= htmlspecialchars($cake['name']) ?></h4>
                                <p class="text-xs text-gray-400 line-clamp-1 max-w-xs"><?= htmlspecialchars($cake['description']) ?></p>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <?= htmlspecialchars($cake['category_name']) ?>
                            </span>
                        </td>

                        <!-- Price -->
                        <td class="py-4 px-6 font-bold text-gray-900 font-serif">
                            $<?= number_format((float)$cake['price'], 2) ?>
                        </td>

                        <!-- Featured Badge -->
                        <td class="py-4 px-6">
                            <?php if ($cake['is_featured']): ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                                    ★ Featured
                                </span>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">Regular</span>
                            <?php endif; ?>
                        </td>

                        <!-- Availability Toggle -->
                        <td class="py-4 px-6">
                            <form action="products.php" method="POST" class="inline">
                                <input type="hidden" name="action" value="toggle_availability">
                                <input type="hidden" name="cake_id" value="<?= $cake['id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $cake['is_available'] ?>">

                                <?php if ($cake['is_available']): ?>
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition" title="Click to toggle out of stock">
                                        In Stock
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 hover:bg-rose-200 transition" title="Click to mark available">
                                        Sold Out
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="product_form.php?id=<?= $cake['id'] ?>" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition">
                                <i class="fa-solid fa-pen mr-1 text-[10px]"></i> Edit
                            </a>

                            <form action="products.php" method="POST" class="inline" onsubmit="return confirm('Delete cake \'<?= htmlspecialchars($cake['name'], ENT_QUOTES) ?>\'?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="cake_id" value="<?= $cake['id'] ?>">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                    <i class="fa-solid fa-trash mr-1 text-[10px]"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
