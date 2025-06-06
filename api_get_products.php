<?php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'whst';
$user = 'student';
$pass = '123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

$faction = $_GET['faction'] ?? 'all';
$category = $_GET['category'] ?? 'all';

$sql = "SELECT product_id as id, name, price, image_path as image FROM products WHERE 1=1";
$params = [];

if ($faction !== 'all' && !empty($faction)) {
    $sql .= " AND faction = :faction";
    $params[':faction'] = $faction;
}

if ($category !== 'all' && !empty($category)) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

echo json_encode($products);