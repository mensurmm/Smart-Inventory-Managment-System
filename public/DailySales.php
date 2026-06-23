<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
require_once '../classes/Database.php';

$db = Database::getInstance()->getConnection();
$today = date('Y-m-d');

// 1. Fetch the Top Selling Product of the Day
$topSellerQuery = "SELECT p.name, SUM(s.quantity_sold) as total_qty 
                   FROM sales_log s 
                   JOIN products p ON s.product_barcode = p.barcode 
                   WHERE DATE(s.sale_date) = :today
                   GROUP BY s.product_barcode 
                   ORDER BY total_qty DESC LIMIT 1";
$stmtTop = $db->prepare($topSellerQuery);
$stmtTop->execute([':today' => $today]);
$topItem = $stmtTop->fetch(PDO::FETCH_ASSOC);

// 2. Fetch the Full Sales Log for Today
$logQuery = "SELECT s.*, p.name, e.name as cashier_name 
             FROM sales_log s 
             JOIN products p ON s.product_barcode = p.barcode 
             JOIN employees e ON s.cashier_id = e.id
             WHERE DATE(s.sale_date) = :today
             ORDER BY s.sale_date DESC";
$stmtLog = $db->prepare($logQuery);
$stmtLog->execute([':today' => $today]);
$salesLog = $stmtLog->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockPilot - Today's Sales Performance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/DailySales.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <main class="inventory-wrapper">
        <div class="inventory-header">
            <a href="Inventory.php" style="text-decoration:none; color:#3498db;">← Back to Dashboard</a>
            <h1>Today's Performance Overview</h1>
        </div>

        <?php if ($topItem): ?>
        <div class="stat-card total" style="max-width: 400px; margin-bottom: 30px; border-left: 5px solid #2ecc71;">
            <div class="card-icon">🏆</div>
            <h3>Top Selling Item Today</h3>
            <span class="value" style="font-size: 1.5rem;"><?= htmlspecialchars($topItem['name']) ?></span>
            <p>Moved <strong><?= $topItem['total_qty'] ?></strong> units today</p>
        </div>
        <?php endif; ?>

        <h3>Detailed Sales Log</h3>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Cashier</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($salesLog)): ?>
                    <tr><td colspan="6" style="text-align:center;">No sales recorded yet today.</td></tr>
                <?php else: ?>
                    <?php foreach ($salesLog as $sale): ?>
                    <tr>
                        <td><?= date('H:i A', strtotime($sale['sale_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['cashier_name']) ?></td>
                        <td><?= htmlspecialchars($sale['name']) ?></td>
                        <td><?= $sale['quantity_sold'] ?></td>
                        <td><strong>ETB <?= number_format($sale['total_price'], 2) ?></strong></td>
                        <td><span class="badge"><?= $sale['payment_method'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>