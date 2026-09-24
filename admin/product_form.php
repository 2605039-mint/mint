<?php
// admin/product_form.php - Add / Edit Cake Item
require_once __DIR__ . '/header.php';

$db = get_db();
$cake_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_editing = $cake_id > 0;

$cake = [
    'name' => '',
    'category_id' => 1,
    'price' => '',
    'image_url' => '',
    'description' => '',
    'is_featured' => 0,
    'is_available' => 1
];

if ($is_editing) {
    $stmt = $db->prepare("SELECT * FROM cakes WHERE id = ?");
    $stmt->execute([$cake_id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $cake = $existing;
    } else {
        header("Location: products.php");
        exit;
    }
}

// Fetch categories for select dropdown
$categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if (empty($name)) {
        $errors[] = "Please provide the cake name.";
    }
    if ($price <= 0) {
        $errors[] = "Please provide a valid price greater than $0.";
    }
    if (empty($image_url)) {
        $errors[] = "Please provide an image URL for the cake.";
    }
    if (empty($description)) {
        $errors[] = "Please provide a description.";
    }

    if (empty($errors)) {
        if ($is_editing) {
            $stmt = $db->prepare("
                UPDATE cakes 
                SET category_id = ?, name = ?, description = ?, price = ?, image_url = ?, is_featured = ?, is_available = ?
                WHERE id = ?
            ");
            $stmt->execute([$category_id, $name, $description, $price, $image_url, $is_featured, $is_available, $cake_id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO cakes (category_id, name, description, price, image_url, is_featured, is_available)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$category_id, $name, $description, $price, $image_url, $is_featured, $is_available]);
        }

        header("Location: products.php");
        exit;
    } else {
        $cake = [
            'name' => $name,
            'category_id' => $category_id,
            'price' => $price,
            'image_url' => $image_url,
            'description' => $description,
            'is_featured' => $is_featured,
            'is_available' => $is_available
        ];
    }
}
?>

<div class="max-w-3xl mx-auto py-4">
    <!-- Back link -->
    <a href="products.php" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-teal-700 mb-6">
        <i class="fa-solid fa-arrow-left mr-1.5"></i> Back to Inventory
    </a>

    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
        <h1 class="font-serif text-2xl font-bold text-gray-900 mb-2">
            <?= $is_editing ? 'Edit Cake Details' : 'Add New Handcrafted Cake' ?>
        </h1>
        <p class="text-xs text-gray-500 mb-8">Fill in the flavor profile, pricing, and visual presentation.</p>

        <?php if (!empty($errors)): ?>
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-xs">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= $is_editing ? 'product_form.php?id=' . $cake_id : 'product_form.php' ?>" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Cake Title *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($cake['name']) ?>" placeholder="e.g. Vanilla Berry Mille Crepe" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Category *</label>
                    <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white font-medium">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cake['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Price ($ USD) *</label>
                    <input type="number" step="0.50" min="1" name="price" required value="<?= htmlspecialchars((string)$cake['price']) ?>" placeholder="32.00" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                </div>
            </div>

            <!-- Image URL -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Photo Image URL *</label>
                <input type="url" id="imageUrlInput" name="image_url" required value="<?= htmlspecialchars($cake['image_url']) ?>" placeholder="https://images.unsplash.com/..." 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                    <span class="font-semibold text-gray-600">Preset photo quick-picks:</span>
                    <button type="button" onclick="setImage('https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=800&q=80')" class="underline hover:text-teal-600">Shortcake</button>
                    <button type="button" onclick="setImage('https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=800&q=80')" class="underline hover:text-teal-600">Mint Ganache</button>
                    <button type="button" onclick="setImage('https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=800&q=80')" class="underline hover:text-teal-600">Cheesecake</button>
                    <button type="button" onclick="setImage('https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?auto=format&fit=crop&w=800&q=80')" class="underline hover:text-teal-600">Chocolate Mousse</button>
                    <button type="button" onclick="setImage('https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=800&q=80')" class="underline hover:text-teal-600">Fruit Tart</button>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Description & Flavor Profile *</label>
                <textarea name="description" rows="4" required placeholder="Describe ingredients, texture, sponge type, and fillings..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"><?= htmlspecialchars($cake['description']) ?></textarea>
            </div>

            <!-- Checkbox toggles -->
            <div class="flex flex-wrap gap-6 pt-2">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" <?= !empty($cake['is_featured']) ? 'checked' : '' ?> 
                           class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                    <span class="text-sm font-semibold text-gray-700">Feature on Homepage (Chef's Pick)</span>
                </label>

                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" <?= !empty($cake['is_available']) ? 'checked' : '' ?> 
                           class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                    <span class="text-sm font-semibold text-gray-700">In Stock / Available for Ordering</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                <a href="products.php" class="px-6 py-3 rounded-xl text-gray-600 hover:bg-gray-100 text-sm font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-md transition">
                    <?= $is_editing ? 'Save Changes' : 'Publish Cake' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setImage(url) {
    document.getElementById('imageUrlInput').value = url;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
