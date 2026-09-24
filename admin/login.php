<?php
// admin/login.php - Admin Staff Login
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_user'] = [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email']
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal Login | Mint Patisserie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-teal-600 text-white mx-auto flex items-center justify-center text-2xl shadow-md mb-4">
                <i class="fa-solid fa-cake-candles"></i>
            </div>
            <h1 class="text-2xl font-bold font-serif text-gray-900">Mint Staff Portal</h1>
            <p class="text-xs text-gray-500 mt-1">Management dashboard for bakery orders & products</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Username or Email</label>
                <div class="relative">
                    <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" required value="admin123" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5">Demo default: username <code>admin</code> / password <code>admin123</code></p>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md transition">
                Sign In to Dashboard
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="../index.php" class="text-xs font-semibold text-teal-600 hover:text-teal-800">
                &larr; Return to Cake Shop
            </a>
        </div>
    </div>
</body>
</html>
