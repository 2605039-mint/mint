<?php
// admin/index.php - Orders & Stats Dashboard
$admin_title = "Bakery Dashboard";
require_once __DIR__ . '/header.php';

$db = get_db();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';

    $allowed = ['pending', 'processing', 'completed', 'cancelled'];
    if (in_array($new_status, $allowed) && $order_id > 0) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $flash_msg = "Order #ORD-" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " status updated to " . ucfirst($new_status);
    }
}

// Stats metrics
$total_revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$active_products = $db->query("SELECT COUNT(*) FROM cakes WHERE is_available = 1")->fetchColumn();

// Fetch orders with filtering
$filter_status = $_GET['status'] ?? 'all';
$order_query = "SELECT * FROM orders";
$order_params = [];

if ($filter_status !== 'all' && in_array($filter_status, ['pending', 'processing', 'completed', 'cancelled'])) {
    $order_query .= " WHERE status = ?";
    $order_params[] = $filter_status;
}
$order_query .= " ORDER BY id DESC";

$stmt = $db->prepare($order_query);
$stmt->execute($order_params);
$orders = $stmt->fetchAll();

// Preload items for all displayed orders
$orders_with_items = [];
foreach ($orders as $order) {
    $item_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $item_stmt->execute([$order['id']]);
    $order['items'] = $item_stmt->fetchAll();
    $orders_with_items[] = $order;
}
?>

<!-- Flash Update Alert -->
<?php if (isset($flash_msg)): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-2 text-sm">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <span><?= htmlspecialchars($flash_msg) ?></span>
    </div>
<?php endif; ?>

<!-- Page Title -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold font-serif text-gray-900">Bakery Performance & Orders</h1>
        <p class="text-sm text-gray-500 mt-1">Real-time overview of customer orders, preparation queues, and revenue.</p>
    </div>
    <a href="product_form.php" class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition">
        <i class="fa-solid fa-plus mr-1.5"></i> Add New Cake
    </a>
</div>

<!-- Metrics Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Total Revenue -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Sales</span>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">$<?= number_format((float)$total_revenue, 2) ?></h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-dollar-sign"></i>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Orders</span>
            <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= (int)$total_orders ?></h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-receipt"></i>
        </div>
    </div>

    <!-- Pending Queue -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Orders</span>
            <h3 class="text-2xl font-bold text-amber-600 mt-1"><?= (int)$pending_orders ?></h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>

    <!-- Available Menu Cakes -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Cakes</span>
            <h3 class="text-2xl font-bold text-teal-600 mt-1"><?= (int)$active_products ?></h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-cake-candles"></i>
        </div>
    </div>
</div>

<!-- Orders Table Card -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filter Tabs -->
    <div class="p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <h2 class="font-serif text-lg font-bold text-gray-900">Customer Cake Orders</h2>
        
        <div class="flex flex-wrap gap-1 bg-gray-100 p-1 rounded-xl text-xs font-semibold">
            <a href="index.php?status=all" class="px-3 py-1.5 rounded-lg <?= $filter_status === 'all' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' ?>">All</a>
            <a href="index.php?status=pending" class="px-3 py-1.5 rounded-lg <?= $filter_status === 'pending' ? 'bg-white text-amber-700 shadow-xs' : 'text-gray-600 hover:text-gray-900' ?>">Pending</a>
            <a href="index.php?status=processing" class="px-3 py-1.5 rounded-lg <?= $filter_status === 'processing' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900' ?>">Baking / Processing</a>
            <a href="index.php?status=completed" class="px-3 py-1.5 rounded-lg <?= $filter_status === 'completed' ? 'bg-white text-emerald-700 shadow-xs' : 'text-gray-600 hover:text-gray-900' ?>">Completed</a>
            <a href="index.php?status=cancelled" class="px-3 py-1.5 rounded-lg <?= $filter_status === 'cancelled' ? 'bg-white text-rose-700 shadow-xs' : 'text-gray-600 hover:text-gray-900' ?>">Cancelled</a>
        </div>
    </div>

    <?php if (empty($orders_with_items)): ?>
        <div class="py-16 text-center text-gray-400">
            <i class="fa-solid fa-inbox text-4xl mb-3"></i>
            <p class="text-sm">No orders found matching this filter.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/75 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-6">Order ID</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Fulfillment</th>
                        <th class="py-3.5 px-6">Items</th>
                        <th class="py-3.5 px-6">Total</th>
                        <th class="py-3.5 px-6">Status & Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($orders_with_items as $ord): ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- Order ID & Date -->
                            <td class="py-4 px-6 align-top">
                                <span class="font-bold text-gray-900 block">#ORD-<?= str_pad($ord['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <span class="text-xs text-gray-400"><?= date('M j, g:i A', strtotime($ord['created_at'])) ?></span>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-4 px-6 align-top">
                                <div class="font-semibold text-gray-900"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($ord['customer_email']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                            </td>

                            <!-- Delivery/Pickup -->
                            <td class="py-4 px-6 align-top">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider mb-1 <?= $ord['delivery_type'] === 'pickup' ? 'bg-purple-100 text-purple-700' : 'bg-cyan-100 text-cyan-700' ?>">
                                    <?= ucfirst($ord['delivery_type']) ?>
                                </span>
                                <?php if ($ord['delivery_type'] === 'pickup'): ?>
                                    <div class="text-xs text-gray-600 font-medium">⏰ <?= htmlspecialchars($ord['pickup_datetime']) ?></div>
                                <?php else: ?>
                                    <div class="text-xs text-gray-600 line-clamp-2">📍 <?= htmlspecialchars($ord['delivery_address']) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Items Summary -->
                            <td class="py-4 px-6 align-top">
                                <ul class="space-y-1 text-xs">
                                    <?php foreach ($ord['items'] as $item): ?>
                                        <li class="text-gray-700">
                                            <strong><?= (int)$item['quantity'] ?>&times;</strong> <?= htmlspecialchars($item['cake_name']) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if (!empty($ord['notes'])): ?>
                                    <div class="mt-2 text-[11px] text-amber-700 bg-amber-50 p-2 rounded-lg border border-amber-100">
                                        <i class="fa-solid fa-pen-fancy mr-1"></i> <?= htmlspecialchars($ord['notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Total -->
                            <td class="py-4 px-6 align-top font-bold text-gray-900">
                                $<?= number_format((float)$ord['total_amount'], 2) ?>
                            </td>

                            <!-- Status & Form -->
                            <td class="py-4 px-6 align-top">
                                <form action="index.php" method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">

                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-xs font-semibold py-1.5 px-2.5 rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                        <option value="pending" <?= $ord['status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                                        <option value="processing" <?= $ord['status'] === 'processing' ? 'selected' : '' ?>>🎂 Processing</option>
                                        <option value="completed" <?= $ord['status'] === 'completed' ? 'selected' : '' ?>>✅ Completed</option>
                                        <option value="cancelled" <?= $ord['status'] === 'cancelled' ? 'selected' : '' ?>>❌ Cancelled</option>
                                    </select>

                                    <a href="../order_success.php?id=<?= $ord['id'] ?>" target="_blank" class="text-gray-400 hover:text-teal-600 p-1" title="View Customer Receipt">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
