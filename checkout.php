<?php
// checkout.php - Customer Checkout & Order Placement
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$db = get_db();
$errors = [];

// Calculate total
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Process order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $delivery_type = $_POST['delivery_type'] ?? 'pickup';
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $pickup_datetime = trim($_POST['pickup_datetime'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Basic Validation
    if (empty($customer_name)) {
        $errors[] = 'Please enter your full name.';
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($customer_phone)) {
        $errors[] = 'Please provide a contact phone number.';
    }
    if ($delivery_type === 'delivery' && empty($delivery_address)) {
        $errors[] = 'Please enter your delivery street address.';
    }
    if ($delivery_type === 'pickup' && empty($pickup_datetime)) {
        $errors[] = 'Please select a preferred pickup date and time.';
    }

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO orders (
                    customer_name, customer_email, customer_phone, 
                    delivery_type, delivery_address, pickup_datetime, 
                    total_amount, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)
            ");
            $stmt->execute([
                $customer_name,
                $customer_email,
                $customer_phone,
                $delivery_type,
                $delivery_address,
                $pickup_datetime,
                $subtotal,
                $notes
            ]);

            $order_id = $db->lastInsertId();

            $item_stmt = $db->prepare("
                INSERT INTO order_items (order_id, cake_id, cake_name, price, quantity, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $item) {
                $item_total = $item['price'] * $item['quantity'];
                $item_stmt->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item_total
                ]);
            }

            $db->commit();

            // Clear session cart
            $_SESSION['cart'] = [];

            // Redirect to confirmation page
            header("Location: order_success.php?id=" . $order_id);
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'An error occurred while saving your order. Please try again: ' . $e->getMessage();
        }
    }
}

$page_title = "Checkout & Order";
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900">Complete Your Cake Order</h1>
        <p class="text-sm text-gray-500 mt-1">Please provide your contact information and pickup / delivery preferences.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl">
            <div class="flex items-center space-x-2 font-bold mb-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                <span>Please fix the following:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                <?php foreach ($errors as $err): ?>
                    <li><?= h($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Order Form (2 Cols) -->
        <div class="lg:col-span-2">
            <form action="checkout.php" method="POST" class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100 space-y-8">
                
                <!-- Step 1: Contact Details -->
                <div>
                    <h2 class="font-serif text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center">
                        <span class="w-7 h-7 rounded-full bg-mint-100 text-mint-700 text-xs font-bold flex items-center justify-center mr-3">1</span>
                        Your Contact Information
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Full Name *</label>
                            <input type="text" name="customer_name" required value="<?= h($_POST['customer_name'] ?? '') ?>" placeholder="e.g. Sarah Jenkins"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Email Address *</label>
                            <input type="email" name="customer_email" required value="<?= h($_POST['customer_email'] ?? '') ?>" placeholder="sarah@example.com"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Phone Number *</label>
                            <input type="tel" name="customer_phone" required value="<?= h($_POST['customer_phone'] ?? '') ?>" placeholder="(555) 000-0000"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Pickup or Delivery -->
                <div>
                    <h2 class="font-serif text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center">
                        <span class="w-7 h-7 rounded-full bg-mint-100 text-mint-700 text-xs font-bold flex items-center justify-center mr-3">2</span>
                        Delivery & Timing Options
                    </h2>

                    <!-- Delivery Type Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <label class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all has-[:checked]:border-mint-600 has-[:checked]:bg-mint-50/50 border-gray-200">
                            <input type="radio" name="delivery_type" value="pickup" checked onchange="toggleDeliveryOptions()" class="text-mint-600 focus:ring-mint-500 mr-3">
                            <div>
                                <div class="font-bold text-gray-900 text-sm">Store Pickup (Free)</div>
                                <div class="text-xs text-gray-500">Pick up at 108 Mint Blvd</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all has-[:checked]:border-mint-600 has-[:checked]:bg-mint-50/50 border-gray-200">
                            <input type="radio" name="delivery_type" value="delivery" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === 'delivery') ? 'checked' : '' ?> onchange="toggleDeliveryOptions()" class="text-mint-600 focus:ring-mint-500 mr-3">
                            <div>
                                <div class="font-bold text-gray-900 text-sm">Refrigerated Courier</div>
                                <div class="text-xs text-gray-500">Direct to your doorstep</div>
                            </div>
                        </label>
                    </div>

                    <!-- Pickup Details -->
                    <div id="pickupSection" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Pickup Date & Time *</label>
                            <input type="datetime-local" name="pickup_datetime" value="<?= h($_POST['pickup_datetime'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500">
                            <p class="text-xs text-gray-400 mt-1">Store hours: 9:00 AM - 7:30 PM. Please allow at least 3 hours for preparation.</p>
                        </div>
                    </div>

                    <!-- Delivery Address Details -->
                    <div id="deliverySection" class="hidden space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Delivery Street Address *</label>
                            <textarea name="delivery_address" rows="3" placeholder="Building, Street, Unit #, City, Postal Code"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500"><?= h($_POST['delivery_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Special Requests / Cake Inscriptions -->
                <div>
                    <h2 class="font-serif text-xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center">
                        <span class="w-7 h-7 rounded-full bg-mint-100 text-mint-700 text-xs font-bold flex items-center justify-center mr-3">3</span>
                        Custom Piping Message & Special Notes
                    </h2>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Chocolate Plaque Inscription / Notes (Optional)</label>
                        <textarea name="notes" rows="3" placeholder="e.g. Chocolate plaque inscription: 'Happy 25th Birthday Emma!'"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500"><?= h($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 rounded-xl bg-mint-600 hover:bg-mint-700 text-white font-bold text-base shadow-xl shadow-mint-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Confirm & Place Order &bull; <?= format_price($subtotal) ?></span>
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-3">Payment can be made in-store on pickup or upon contactless delivery.</p>
                </div>
            </form>
        </div>

        <!-- Sidebar Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 sticky top-28 space-y-6">
                <h3 class="font-serif text-xl font-bold text-gray-900 pb-4 border-b border-gray-100">Your Basket (<?= count($_SESSION['cart']) ?>)</h3>

                <div class="space-y-4 max-h-80 overflow-y-auto pr-1">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="flex items-center space-x-3 text-sm">
                            <img src="<?= h($item['image_url']) ?>" alt="<?= h($item['name']) ?>" class="w-12 h-12 rounded-xl object-cover shadow-xs">
                            <div class="flex-grow">
                                <h4 class="font-semibold text-gray-900 text-xs line-clamp-1"><?= h($item['name']) ?></h4>
                                <span class="text-xs text-gray-500">Qty: <?= (int)$item['quantity'] ?> &times; <?= format_price($item['price']) ?></span>
                            </div>
                            <span class="font-bold text-gray-900 text-xs"><?= format_price($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900"><?= format_price($subtotal) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Pickup Fee</span>
                        <span class="font-semibold text-emerald-600">FREE</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-baseline font-bold text-gray-900">
                        <span class="text-base">Total Due</span>
                        <span class="font-serif text-2xl text-mint-800"><?= format_price($subtotal) ?></span>
                    </div>
                </div>

                <a href="cart.php" class="block text-center text-xs font-semibold text-mint-700 hover:underline">
                    Edit Shopping Bag
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDeliveryOptions() {
    const isDelivery = document.querySelector('input[name="delivery_type"]:checked').value === 'delivery';
    const deliverySection = document.getElementById('deliverySection');
    const pickupSection = document.getElementById('pickupSection');

    if (isDelivery) {
        deliverySection.classList.remove('hidden');
    } else {
        deliverySection.classList.add('hidden');
    }
}
toggleDeliveryOptions();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
