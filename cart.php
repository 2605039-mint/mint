<?php
// cart.php - Shopping Cart Handler & Display
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$db = get_db();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $cake_id = (int)($_POST['cake_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($cake_id > 0) {
            $stmt = $db->prepare("SELECT id, name, price, image_url FROM cakes WHERE id = ? AND is_available = 1");
            $stmt->execute([$cake_id]);
            $cake = $stmt->fetch();

            if ($cake) {
                if (isset($_SESSION['cart'][$cake_id])) {
                    $_SESSION['cart'][$cake_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$cake_id] = [
                        'id' => $cake['id'],
                        'name' => $cake['name'],
                        'price' => (float)$cake['price'],
                        'image_url' => $cake['image_url'],
                        'quantity' => $quantity
                    ];
                }
                $_SESSION['flash_success'] = 'Added "' . $cake['name'] . '" to your cake bag!';
            }
        }
        header("Location: cart.php");
        exit;
    }

    if ($action === 'update') {
        $quantities = $_POST['quantities'] ?? [];
        foreach ($quantities as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } elseif (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] = min(20, $qty);
            }
        }
        header("Location: cart.php");
        exit;
    }
}

// Handle GET removals
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Calculate cart totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$page_title = "Your Cake Bag";
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900">Your Shopping Bag</h1>
        <p class="text-sm text-gray-500 mt-1">Review your handcrafted cakes before selecting pickup or delivery.</p>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm max-w-2xl mx-auto">
            <div class="w-20 h-20 rounded-full bg-mint-50 text-mint-600 mx-auto flex items-center justify-center text-3xl mb-4">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2 class="font-serif text-2xl font-bold text-gray-900 mb-2">Your Bag is Empty</h2>
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                Looks like you haven't added any sweet treats yet. Take a look at our daily cakes and seasonal tarts!
            </p>
            <a href="menu.php" class="inline-flex items-center px-8 py-3.5 rounded-xl bg-mint-600 hover:bg-mint-700 text-white font-semibold shadow-md transition">
                <i class="fa-solid fa-cake-candles mr-2"></i> Browse Our Cakes
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Items Table/List -->
            <div class="lg:col-span-2">
                <form action="cart.php" method="POST" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <input type="hidden" name="action" value="update">

                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <span class="font-semibold text-gray-800 text-sm">Cakes Selected (<?= count($_SESSION['cart']) ?>)</span>
                        <a href="cart.php?clear=1" onclick="return confirm('Empty your shopping bag?')" class="text-xs text-rose-500 hover:text-rose-700 font-medium">
                            <i class="fa-solid fa-trash mr-1"></i> Empty Bag
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <?php $line_total = $item['price'] * $item['quantity']; ?>
                            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center space-x-4 w-full sm:w-auto">
                                    <img src="<?= h($item['image_url']) ?>" alt="<?= h($item['name']) ?>" class="w-20 h-20 rounded-2xl object-cover flex-shrink-0 shadow-sm">
                                    <div>
                                        <h3 class="font-serif font-bold text-gray-900 text-base leading-snug">
                                            <a href="cake.php?id=<?= $id ?>" class="hover:text-mint-700 transition"><?= h($item['name']) ?></a>
                                        </h3>
                                        <div class="text-xs text-gray-500 mt-1">Unit Price: <?= format_price($item['price']) ?></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end space-x-6 w-full sm:w-auto">
                                    <!-- Quantity Input -->
                                    <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden">
                                        <input type="number" name="quantities[<?= $id ?>]" value="<?= (int)$item['quantity'] ?>" min="1" max="20" 
                                               class="w-14 text-center bg-transparent py-1.5 font-bold text-sm text-gray-900 focus:outline-none">
                                    </div>

                                    <!-- Line total -->
                                    <div class="w-24 text-right">
                                        <span class="font-serif font-bold text-lg text-gray-900"><?= format_price($line_total) ?></span>
                                    </div>

                                    <!-- Delete Button -->
                                    <a href="cart.php?remove=<?= $id ?>" class="text-gray-400 hover:text-rose-500 p-2 transition" title="Remove item">
                                        <i class="fa-solid fa-xmark text-base"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="p-6 bg-gray-50/70 border-t border-gray-100 flex justify-between items-center">
                        <a href="menu.php" class="text-sm font-semibold text-mint-700 hover:text-mint-800">
                            &larr; Add More Cakes
                        </a>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-100 shadow-sm transition">
                            <i class="fa-solid fa-rotate mr-1.5"></i> Update Quantities
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 sticky top-28 space-y-6">
                    <h2 class="font-serif text-xl font-bold text-gray-900 pb-4 border-b border-gray-100">Order Summary</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900"><?= format_price($subtotal) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Store Pickup</span>
                            <span class="font-medium text-emerald-600">Free</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Applicable Taxes</span>
                            <span class="text-gray-400 italic">Included</span>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex justify-between items-baseline">
                            <span class="font-bold text-gray-900 text-base">Estimated Total</span>
                            <span class="font-serif font-bold text-2xl text-mint-800"><?= format_price($subtotal) ?></span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="checkout.php" class="w-full inline-flex items-center justify-center px-6 py-4 rounded-xl bg-mint-600 hover:bg-mint-700 text-white font-bold shadow-lg shadow-mint-500/25 transition transform hover:-translate-y-0.5">
                            <span>Proceed to Checkout</span>
                            <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <div class="bg-rosecream-50 p-4 rounded-2xl border border-rosecream-100 text-xs text-gray-500 space-y-2">
                        <div class="flex items-center space-x-2 text-mint-700 font-semibold">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Freshness Guarantee</span>
                        </div>
                        <p>All cakes are packaged in insulated boxes with cold packs to ensure peak quality.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
