<?php
// db.php - SQLite Database Connection & API Handler
header("Content-Type: application/json; charset=UTF-8");

$dbFile = 'database.sqlite';
try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // টেবিল তৈরি
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        phone TEXT PRIMARY KEY,
        name TEXT,
        password TEXT,
        businessName TEXT,
        address TEXT,
        profilePic TEXT,
        coverPic TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS ads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        category TEXT,
        location TEXT,
        price REAL,
        sellerPhone TEXT,
        seller TEXT,
        status TEXT,
        image TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS chats (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        adId INTEGER,
        adTitle TEXT,
        buyerPhone TEXT,
        buyerName TEXT,
        sellerPhone TEXT,
        sellerName TEXT,
        messages TEXT
    )");

    $action = $_GET['action'] ?? '';

    if ($action === 'save_user') {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO users (phone, name, password, businessName, address, profilePic, coverPic) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['phone'], $data['name'], $data['password'], 
            $data['businessName'], $data['address'], 
            $data['profilePic'] ?? '', $data['coverPic'] ?? ''
        ]);
        echo json_encode(["status" => "success"]);
        exit;
    }

    if ($action === 'get_users') {
        $stmt = $pdo->query("SELECT * FROM users");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'save_ad') {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO ads (title, category, location, price, sellerPhone, seller, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'], $data['category'], $data['location'], 
            $data['price'], $data['sellerPhone'], $data['seller'], 
            $data['status'], $data['image']
        ]);
        echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
        exit;
    }

    if ($action === 'get_ads') {
        $stmt = $pdo->query("SELECT * FROM ads");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>