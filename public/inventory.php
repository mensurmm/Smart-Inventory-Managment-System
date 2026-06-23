<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
require_once '../classes/Database.php';
$db = Database::getInstance()->getConnection();

$query = "SELECT 
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT SUM(quantity_sold) FROM sales_log WHERE DATE(sale_date) = CURDATE()) as sales_today,
    (SELECT COUNT(*) FROM products WHERE quantity <= min_stock_level AND quantity > 0) as low_stock,
    (SELECT COUNT(*) FROM products WHERE quantity = 0) as out_of_stock,
    (SELECT COUNT(DISTINCT product_barcode) FROM stock_batches WHERE expiry_date < CURDATE()) as expired,
    (SELECT COUNT(DISTINCT product_barcode) FROM stock_batches WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH)) as expiring_soon";

$stats = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$sales_today = $stats['sales_today'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StockPilot - Inventory Overview</title>
  <link rel="stylesheet" href="../assets/css/inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
  <?php include '../templates/header.php'; ?>

 <main class="inventory-wrapper">
    <div class="inventory-header">
        <h1>Inventory Health</h1>
        <p>Real-time status of your supermarket stock.</p>
    </div>

    <div class="inventory-summary-grid">
        <div class="stat-card total">
            <div class="card-icon">📊</div>
            <h3>Total Products</h3>
            <span class="value"><?= $stats['total_products'] ?></span>
            <p>Active items in catalog</p>
        </div>

       <a href="DailySales.php" class="stat-card sold clickable">
    <div class="card-icon">💹</div>
    <h3>Sold Today</h3>
    <span class="value"><?= $sales_today ?></span>
    <p>Units moved since morning</p>
</a>

        <a href="InventoryDashboardDetail.php?filter=low_stock" class="stat-card low-stock clickable">
            <div class="card-icon">📉</div>
            <h3>Low Stock</h3>
            <span class="value"><?= $stats['low_stock'] ?></span>
            <p>Items below minimum level</p>
        </a>

        <a href="InventoryDashboardDetail.php?filter=expiring" class="stat-card expiring clickable">
            <div class="card-icon">⚠️</div>
            <h3>Expiring Soon</h3>
            <span class="value"><?= $stats['expiring_soon'] ?></span>
            <p>Expires within 2 months</p>
        </a>

        <a href="InventoryDashboardDetail.php?filter=expired" class="stat-card expired clickable">
            <div class="card-icon">🛑</div>
            <h3>Expired Items</h3>
            <span class="value"><?= $stats['expired'] ?></span>
            <p>Remove from shelf immediately</p>
        </a>
        <a href="InventoryDashboardDetail.php?filter=out_of_stock" class="stat-card out-of-stock clickable">
    <div class="card-icon" id="outofstock">🚫</div>
    <h3>Out of Stock</h3>
    <span class="value"><?= $stats['out_of_stock'] ?></span>
    <p>Zero units available</p>
</a>
    </div>
</main>
  </main>
</body>
</html>
