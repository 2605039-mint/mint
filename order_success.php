<?php
// order_success.php - Order Confirmation Receipt
require_once __DIR__ . '/includes/db.php';

$db = get_db();
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

$items_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll();

$page_title = "Order Confirmed #" . $order_id;
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <!-- Success Banner -->
    <div class="text-center space-y-4 mb-10">
        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl shadow-sm">
            <i class="fa-solid fa-check"></i>
        </div>
        <span class="text-xs font-bold text-mint-700 uppercase tracking-widest">Order Received</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900">Thank You For Your Order!</h1>
        <p class="text-sm text-gray-600 max-w-md mx-auto">
            We've received your request and our bakers are preparing your items. A confirmation has been logged for pickup / delivery.
        </p>
    </div>

    <!-- Order Receipt Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100 space-y-8">
        <!-- Order Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-gray-100 gap-4">
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Order Reference</span>
                <h3 class="font-serif text-2xl font-bold text-gray-900">#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h3>
                <span class="text-xs text-gray-500">Placed on <?= date('F j, Y, g:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div>
                <?php
                $status_colors = [
                    'pending' => 'bg-amber-100 text-amber-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'completed' => 'bg-emerald-100 text-emerald-800',
                    'cancelled' => 'bg-rose-100 text-rose-800'
                ];
                $badge_color = $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider <?= $badge_color ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
        </div>

        <!-- Customer & Pickup Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
            <div class="space-y-1">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Customer Details</h4>
                <p class="font-semibold text-gray-900"><?= h($order['customer_name']) ?></p>
                <p class="text-gray-600"><?= h($order['customer_email']) ?></p>
                <p class="text-gray-600"><?= h($order['customer_phone']) ?></p>
            </div>

            <div class="space-y-1">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Fulfillment Method</h4>
                <p class="font-semibold text-gray-900">
                    <?= $order['delivery_type'] === 'pickup' ? 'Store Pickup (108 Mint Blvd)' : 'Refrigerated Courier Delivery' ?>
                </p>
                <?php if ($order['delivery_type'] === 'pickup'): ?>
                    <p class="text-gray-600"><strong>Pickup Time:</strong> <?= h($order['pickup_datetime']) ?></p>
                <?php else: ?>
                    <p class="text-gray-600"><strong>Address:</strong> <?= nl2br(h($order['delivery_address'])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($order['notes'])): ?>
            <div class="bg-rosecream-50 p-4 rounded-2xl border border-rosecream-100 text-xs">
                <span class="font-bold text-gray-700 block mb-1">Custom Message / Inscription Note:</span>
                <p class="text-gray-600 leading-relaxed"><?= nl2br(h($order['notes'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Order Items List -->
        <div>
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Ordered Cakes</h4>
            <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                <?php foreach ($items as $item): ?>
                    <div class="py-3 flex justify-between items-center text-sm">
                        <div>
                            <span class="font-semibold text-gray-900"><?= h($item['cake_name']) ?></span>
                            <span class="text-xs text-gray-500 block">&times; <?= $item['quantity'] ?> (<?= format_price($item['price']) ?> each)</span>
                        </div>
                        <span class="font-bold text-gray-900"><?= format_price($item['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-4 flex justify-between items-baseline font-bold text-gray-900">
                <span class="text-base">Total Amount</span>
                <span class="font-serif text-2xl text-mint-800"><?= format_price($order['total_amount']) ?></span>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="index.php" class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition">
                Return to Home
            </a>
            <button onclick="window.print()" class="w-full sm:w-auto text-center px-6 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold transition">
                <i class="fa-solid fa-print mr-1.5"></i> Print Receipt
            </button>
            <a href="menu.php" class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-mint-600 hover:bg-mint-700 text-white text-sm font-semibold shadow transition">
                Browse More Cakes
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
