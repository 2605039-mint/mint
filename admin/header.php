<?php
// admin/header.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth.php';
check_admin_auth();

$admin = $_SESSION['admin_user'];
$active_tab = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($admin_title) ? htmlspecialchars($admin_title) . ' | Admin' : 'Bakery Admin | Mint Patisserie' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Admin Top Navbar -->
    <header class="bg-slate-900 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-500 text-slate-900 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-cake-candles"></i>
                        </div>
                        <span class="font-bold text-lg tracking-tight">Mint Bakery Admin</span>
                    </a>

                    <!-- Nav items -->
                    <nav class="hidden md:flex space-x-2 text-sm font-medium">
                        <a href="index.php" class="px-3 py-2 rounded-lg transition <?= $active_tab === 'index.php' ? 'bg-slate-800 text-teal-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                            <i class="fa-solid fa-receipt mr-1.5"></i> Orders & Stats
                        </a>
                        <a href="products.php" class="px-3 py-2 rounded-lg transition <?= ($active_tab === 'products.php' || $active_tab === 'product_form.php') ? 'bg-slate-800 text-teal-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                            <i class="fa-solid fa-boxes-stacked mr-1.5"></i> Cake Inventory
                        </a>
                        <a href="product_form.php" class="px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <i class="fa-solid fa-plus mr-1.5 text-teal-400"></i> Add New Cake
                        </a>
                    </nav>
                </div>

                <!-- Right items -->
                <div class="flex items-center space-x-4 text-xs font-medium">
                    <a href="../index.php" target="_blank" class="hidden sm:inline-flex items-center text-slate-300 hover:text-teal-400 transition">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Live Store
                    </a>
                    <div class="h-4 w-px bg-slate-700 hidden sm:block"></div>
                    <span class="text-slate-400 hidden sm:inline">User: <strong class="text-white"><?= htmlspecialchars($admin['username']) ?></strong></span>
                    <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-300 hover:bg-rose-600/30 transition border border-rose-500/30">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
