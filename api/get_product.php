<?php
session_start();
require_once '../classes/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['barcode'])) {
    $barcode = trim($_GET['barcode']); // Clean spaces
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT barcode, name, selling_price FROM products WHERE barcode = ?");
    $stmt->execute([$barcode]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode(['success' => true, 'data' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No barcode']);
}