<?php
session_start();
require_once '../classes/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    foreach ($data['items'] as $item) {
        $barcode = $item['barcode'];
        $qtyToSell = intval($item['qty']);

        // 1. VALIDATION: Check if enough TOTAL stock exists
        $stmt_check = $db->prepare("SELECT quantity FROM products WHERE barcode = ? FOR UPDATE");
        $stmt_check->execute([$barcode]);
        $currentProduct = $stmt_check->fetch();

        if (!$currentProduct || $currentProduct['quantity'] < $qtyToSell) {
            throw new Exception("Insufficient stock for product: " . $item['name']);
        }

        // 2. DEDUCT from batches (FIFO: Oldest Expiry First)
        $stmt_batches = $db->prepare("SELECT id, quantity FROM stock_batches WHERE product_barcode = ? AND quantity > 0 ORDER BY expiry_date ASC");
        $stmt_batches->execute([$barcode]);
        $batches = $stmt_batches->fetchAll();

        $remainingToDeduct = $qtyToSell;
        foreach ($batches as $batch) {
            if ($remainingToDeduct <= 0) break;

            if ($batch['quantity'] >= $remainingToDeduct) {
                // This batch has enough
                $stmt_update_batch = $db->prepare("UPDATE stock_batches SET quantity = quantity - ? WHERE id = ?");
                $stmt_update_batch->execute([$remainingToDeduct, $batch['id']]);
                $remainingToDeduct = 0;
            } else {
                // Use all of this batch and move to next
                $remainingToDeduct -= $batch['quantity'];
                $stmt_update_batch = $db->prepare("UPDATE stock_batches SET quantity = 0 WHERE id = ?");
                $stmt_update_batch->execute([$batch['id']]);
            }
        }

        // 3. DEDUCT from main products table
        $stmt_prod = $db->prepare("UPDATE products SET quantity = quantity - ? WHERE barcode = ?");
        $stmt_prod->execute([$qtyToSell, $barcode]);

        // 4. LOG the sale
        $stmt_log = $db->prepare("INSERT INTO sales_log (cashier_id, product_barcode, quantity_sold, total_price, payment_method, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_log->execute([
            $_SESSION['user_id'],
            $barcode,
            $qtyToSell,
            ($qtyToSell * $item['price']),
            $data['method']
        ]);
    }

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}