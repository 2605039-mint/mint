<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
$cart_count = get_cart_count();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? h($page_title) . ' | Mint Patisserie' : 'Mint Patisserie & Artisan Cakes' ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mint: {
                            50: '#f0fdf9',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        rosecream: {
                            50: '#fffbf9',
                            100: '#fdf2ec',
                            200: '#fbe2d5',
                            500: '#e07a5f',
                        }
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-rosecream-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="bg-mint-700 text-white text-xs sm:text-sm py-2 px-4 text-center tracking-wide font-medium flex items-center justify-center space-x-2">
        <span>🎂 Fresh handcrafted cakes baked daily &bull; Free pickup at store &bull; Order 24h in advance</span>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-mint-500 to-mint-300 flex items-center justify-center text-white text-xl shadow-md group-hover:rotate-12 transition-transform duration-300">
                        <i class="fa-solid fa-cake-candles"></i>
                    </div>
                    <div>
                        <span class="font-serif text-2xl font-bold tracking-tight text-gray-900 block leading-tight">MINT</span>
                        <span class="text-xs uppercase tracking-widest text-mint-600 font-semibold">Patisserie & Cakes</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="index.php" class="<?= $current_page === 'index.php' ? 'text-mint-700 font-semibold' : 'text-gray-600 hover:text-mint-600' ?> transition-colors">
                        Home
                    </a>
                    <a href="menu.php" class="<?= $current_page === 'menu.php' ? 'text-mint-700 font-semibold' : 'text-gray-600 hover:text-mint-600' ?> transition-colors">
                        Cake Menu
                    </a>
                    <a href="index.php#about" class="text-gray-600 hover:text-mint-600 transition-colors">
                        Our Story
                    </a>
                    <a href="index.php#contact" class="text-gray-600 hover:text-mint-600 transition-colors">
                        Hours & Location
                    </a>
                </nav>

                <!-- Actions: Cart & Admin Link -->
                <div class="flex items-center space-x-4">
                    <!-- Cart Button -->
                    <a href="cart.php" class="relative inline-flex items-center justify-center p-2.5 rounded-full text-gray-700 hover:text-mint-700 hover:bg-mint-50 transition-colors">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs font-bold rounded-full h-5 min-w-[20px] px-1 flex items-center justify-center shadow">
                                <?= $cart_count ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Admin Portal Link -->
                    <a href="admin/login.php" class="hidden sm:inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-mint-700 transition-colors">
                        <i class="fa-solid fa-lock text-xs mr-1.5 text-gray-400"></i> Staff Portal
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-gray-900 p-2 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-gray-200 px-4 pt-2 pb-4 space-y-2">
            <a href="index.php" class="block py-2 text-base font-medium text-gray-700 hover:text-mint-600">Home</a>
            <a href="menu.php" class="block py-2 text-base font-medium text-gray-700 hover:text-mint-600">Cake Menu</a>
            <a href="index.php#about" class="block py-2 text-base font-medium text-gray-700 hover:text-mint-600">Our Story</a>
            <a href="index.php#contact" class="block py-2 text-base font-medium text-gray-700 hover:text-mint-600">Hours & Location</a>
            <a href="cart.php" class="block py-2 text-base font-medium text-mint-700">Shopping Cart (<?= $cart_count ?> items)</a>
            <a href="admin/login.php" class="block py-2 text-sm text-gray-500">Staff Portal</a>
        </div>
    </header>

    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
    </script>

    <!-- Main Content Area -->
    <main class="flex-grow">
