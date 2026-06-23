<?php
require_once '../classes/Database.php';
require_once '../classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productObj = new Product();

    // 1. Capture and Sanitize Form Data
    $barcode = trim($_POST['barcode']);
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $selling_price = floatval($_POST['selling_price']);
    
    $supplier_id = intval($_POST['supplier_id']);
    $quantity = intval($_POST['quantity']);
    $cost_price = floatval($_POST['cost_price']);
    $arrival_date = $_POST['arrival_date'];
    $expiry_date = $_POST['expiry_date'];

    // 2. Register or Update the Product Identity
    $registered = $productObj->register($barcode, $name, $category_id, $selling_price);

    if ($registered) {
        // 3. Add the Batch entry (which now also updates product quantity)
        $batchAdded = $productObj->addBatch($barcode, $supplier_id, $quantity, $cost_price, $arrival_date, $expiry_date);
        
        if ($batchAdded) {
            header("Location: ../public/Inventory.php?success=stock_updated");
        } else {
            header("Location: ../public/NewOrder.php?error=batch_failed");
        }
    } else {
        header("Location: ../public/NewOrder.php?error=product_failed");
    }
    exit();
}