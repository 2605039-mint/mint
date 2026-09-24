    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-12 mt-20 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Brand & About -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-mint-500 flex items-center justify-center text-white text-lg">
                            <i class="fa-solid fa-cake-candles"></i>
                        </div>
                        <span class="font-serif text-2xl font-bold text-white">MINT</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Handcrafted artisan cakes, French fruit tarts, and delicate confections baked fresh every morning using the finest natural ingredients.
                    </p>
                    <div class="flex space-x-4 pt-2">
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-mint-400 hover:bg-gray-700 transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-mint-400 hover:bg-gray-700 transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-mint-400 hover:bg-gray-700 transition"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 font-serif">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="index.php" class="hover:text-mint-400 transition">Home</a></li>
                        <li><a href="menu.php" class="hover:text-mint-400 transition">Our Cake Menu</a></li>
                        <li><a href="cart.php" class="hover:text-mint-400 transition">Shopping Bag</a></li>
                        <li><a href="admin/login.php" class="hover:text-mint-400 transition">Staff Login</a></li>
                    </ul>
                </div>

                <!-- Opening Hours -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 font-serif">Opening Hours</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex justify-between"><span>Mon – Fri:</span> <span class="text-white">9:00 AM – 7:30 PM</span></li>
                        <li class="flex justify-between"><span>Saturday:</span> <span class="text-white">9:30 AM – 8:00 PM</span></li>
                        <li class="flex justify-between"><span>Sunday:</span> <span class="text-white">10:00 AM – 6:00 PM</span></li>
                    </ul>
                    <p class="text-xs text-mint-400 mt-3"><i class="fa-solid fa-bell mr-1"></i> Custom cake orders require 24-48h notice.</p>
                </div>

                <!-- Contact & Location -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 font-serif">Store Location</h3>
                    <p class="text-sm text-gray-400 leading-relaxed mb-3">
                        <i class="fa-solid fa-location-dot text-mint-400 mr-2"></i> 108 Mint Boulevard, Suite 12<br>
                        Pastry District, Metropolis
                    </p>
                    <p class="text-sm text-gray-400 mb-1">
                        <i class="fa-solid fa-phone text-mint-400 mr-2"></i> (555) 321-CAKE (2253)
                    </p>
                    <p class="text-sm text-gray-400">
                        <i class="fa-solid fa-envelope text-mint-400 mr-2"></i> orders@mintcakes.com
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; <?= date('Y') ?> Mint Patisserie & Cakes. All rights reserved.</p>
                <p class="mt-4 md:mt-0">Crafted with PHP & SQLite Database.</p>
            </div>
        </div>
    </footer>
</body>
</html>
