<?php
// includes/db.php - Database connection and automatic SQLite initialization

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbDir = __DIR__ . '/../database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0777, true);
    }

    $dbPath = $dbDir . '/cake_shop.sqlite';
    $isNewDb = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize tables and default seed data if database is fresh
    if ($isNewDb || filesize($dbPath) === 0) {
        init_cake_shop_database($pdo);
    }

    return $pdo;
}

function init_cake_shop_database(PDO $pdo): void {
    // 1. Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            icon TEXT,
            description TEXT
        );

        CREATE TABLE IF NOT EXISTS cakes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            price REAL NOT NULL,
            image_url TEXT NOT NULL,
            is_featured INTEGER DEFAULT 0,
            is_available INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_name TEXT NOT NULL,
            customer_email TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            delivery_type TEXT NOT NULL, -- 'pickup' or 'delivery'
            delivery_address TEXT,
            pickup_datetime TEXT,
            total_amount REAL NOT NULL,
            status TEXT CHECK(status IN ('pending', 'processing', 'completed', 'cancelled')) DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            cake_id INTEGER,
            cake_name TEXT NOT NULL,
            price REAL NOT NULL,
            quantity INTEGER NOT NULL,
            subtotal REAL NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        );
    ");

    // 2. Seed default admin (username: admin, password: admin123)
    $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([
        'admin',
        'admin@mintcakes.com',
        password_hash('admin123', PASSWORD_DEFAULT)
    ]);

    // 3. Seed categories
    $categories = [
        ['Shortcakes & Sponges', 'shortcakes', 'fa-cake-candles', 'Soft, airy sponge cakes layered with fresh whipped cream and fruit.'],
        ['Cheesecakes', 'cheesecakes', 'fa-cheese', 'Rich, creamy baked and Basque cheesecakes with smooth textures.'],
        ['Chocolate & Gateau', 'chocolate', 'fa-cookie-bite', 'Decadent Belgian chocolate, mousse, and layered fudge cakes.'],
        ['Fruit Tarts', 'tarts', 'fa-apple-whole', 'Crisp butter crusts filled with silky pastry cream and fresh seasonal berries.'],
        ['Specialties & Cupcakes', 'specialties', 'fa-sparkles', 'Signature bakery specials, artisan macarons, and decorative cupcakes.']
    ];

    $catStmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $catStmt->execute($cat);
    }

    // 4. Seed cakes
    $cakes = [
        [
            1,
            'Classic Strawberry Shortcake',
            'Fluffy Japanese chiffon sponge layered with fresh Hokkaido whipped cream and sweet organic strawberries.',
            32.00,
            'https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            1,
            'Signature Mint Choco-Drip Cake',
            'Refreshing infused mint sponge dressed in pastel mint buttercream and finished with dark chocolate ganache drip.',
            38.50,
            'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            2,
            'Basque Burnt Cheesecake',
            'Caramelized caramelized exterior with an irresistibly molten, custardy center. Made with authentic cream cheese.',
            29.00,
            'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            2,
            'New York Berry Swirl Cheesecake',
            'Dense, velvety New York cheesecake topped with homemade raspberry reduction and fresh blueberries.',
            31.00,
            'https://images.unsplash.com/photo-1524351199678-941a58a3df50?auto=format&fit=crop&w=800&q=80',
            0,
            1
        ],
        [
            3,
            'Triple Belgian Chocolate Mousse',
            'Layers of dark 70% chocolate, milk chocolate mousse, and white vanilla cream on a crisp praline wafer base.',
            35.00,
            'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            3,
            'Dark Forest Gateau',
            'Moist chocolate cake infused with Kirsch liqueur, filled with sour black cherries and whipped Chantilly cream.',
            36.00,
            'https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?auto=format&fit=crop&w=800&q=80',
            0,
            1
        ],
        [
            4,
            'Fresh Mixed Berry Fruit Tart',
            'Golden French sable crust filled with fragrant vanilla bean crème pâtissière and glazed berries.',
            34.00,
            'https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            4,
            'Zesty Lemon Meringue Tart',
            'Tangy Sicilian lemon curd crowned with toasted golden Italian meringue peaks in a buttery crust.',
            28.00,
            'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?auto=format&fit=crop&w=800&q=80',
            0,
            1
        ],
        [
            5,
            'Kyoto Uji Matcha Chiffon Roll',
            'Premium stone-ground green tea sponge rolled with Azuki red beans and light matcha mascarpone cream.',
            26.00,
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80',
            1,
            1
        ],
        [
            5,
            'Pastel Macaron Gift Box (12 pcs)',
            'Assorted French macarons: Pistachio, Mint Chocolate, Raspberry, Salted Caramel, Earl Grey, and Vanilla.',
            22.00,
            'https://images.unsplash.com/photo-1569864358642-9d1684040f43?auto=format&fit=crop&w=800&q=80',
            0,
            1
        ]
    ];

    $cakeStmt = $pdo->prepare("
        INSERT INTO cakes (category_id, name, description, price, image_url, is_featured, is_available)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($cakes as $cake) {
        $cakeStmt->execute($cake);
    }
}

// Global helper functions
function h(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function format_price(float $price): string {
    return '$' . number_format($price, 2);
}

function get_cart_count(): int {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)($item['quantity'] ?? 0);
        }
    }
    return $count;
}
