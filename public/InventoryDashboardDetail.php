<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php

require_once '../classes/Database.php';

$db = Database::getInstance()->getConnection();
$filter = $_GET['filter'] ?? 'expiring'; 

$title = "Inventory Detail";
$items = [];

if ($filter == 'expiring') {
    $title = "Expiring Soon (Next 60 Days)";
    $sql = "SELECT p.name, p.barcode, b.id as batch_id, b.quantity, b.expiry_date 
            FROM products p 
            JOIN stock_batches b ON p.barcode = b.product_barcode 
            WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH)
            AND b.quantity > 0 ORDER BY b.expiry_date ASC";
} elseif ($filter == 'expired') {
    $title = "Expired Products (Immediate Action)";
    $sql = "SELECT p.name, p.barcode, b.id as batch_id, b.quantity, b.expiry_date 
            FROM products p 
            JOIN stock_batches b ON p.barcode = b.product_barcode 
            WHERE b.expiry_date < CURDATE() AND b.quantity > 0 ORDER BY b.expiry_date ASC";
} elseif ($filter == 'low_stock') {
    $title = "Low Stock Items";
    $sql = "SELECT name, barcode, 'N/A' as batch_id, quantity, 'N/A' as expiry_date 
            FROM products WHERE quantity <= min_stock_level AND quantity > 0";
} elseif ($filter == 'out_of_stock') {
    $title = "Out of Stock (Zero Quantity)";
    $sql = "SELECT name, barcode, 'N/A' as batch_id, quantity, 'N/A' as expiry_date 
            FROM products WHERE quantity = 0";
}

$stmt = $db->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - <?= $title ?></title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/InventoryDashboardDetail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="inventory-wrapper">
        <div class="inventory-header">
            <a href="Inventory.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            <h1><?= $title ?></h1>
            <p>Found <?= count($items) ?> items requiring attention.</p>
        </div>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Barcode</th>
                    <th>Batch ID</th>
                    <th>Current Qty</th>
                    <th>Expiry Date</th>
                    <th>Status / Days</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $daysDiff = "N/A";
                    $rowClass = "";
                    
                    if ($item['expiry_date'] !== 'N/A') {
                        $expiry = new DateTime($item['expiry_date']);
                        $today = new DateTime();
                        $diff = $today->diff($expiry);
                        $daysDiff = $diff->format("%r%a"); 
                        
                        if ($daysDiff < 0) $rowClass = "row-expired";
                        elseif ($daysDiff <= 14) $rowClass = "row-urgent";
                        elseif ($daysDiff <= 60) $rowClass = "row-warning";
                    }
                ?>
                <tr class="<?= $rowClass ?>">
                    <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                    <td><?= htmlspecialchars($item['barcode']) ?></td>
                    <td><?= ($item['batch_id'] !== 'N/A') ? '#' . $item['batch_id'] : '—' ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= ($item['expiry_date'] !== 'N/A') ? date('M d, Y', strtotime($item['expiry_date'])) : '—' ?></td>
                    <td>
                        <?php if($daysDiff !== "N/A"): ?>
                            <span class="days-text"><?= $daysDiff ?> days</span>
                        <?php else: ?>
                            <span class="badge">Check Stock</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>