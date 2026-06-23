<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php 
require_once '../classes/Database.php';
$pdo = Database::getInstance()->getConnection();

$cat_id = $_GET['id'] ?? null;

if (!$cat_id) {
    header("Location: CategoryExplorer.php");
    exit();
}

// Get Category Name for the title
$stmtCat = $pdo->prepare("SELECT category_name FROM categories WHERE id = ?");
$stmtCat->execute([$cat_id]);
$category = $stmtCat->fetch();

// Get Products and their total stock/avg cost
$sql = "SELECT p.*, 
        IFNULL(SUM(sb.quantity), 0) as current_stock,
        IFNULL(AVG(sb.cost_price), 0) as avg_cost
        FROM products p
        LEFT JOIN stock_batches sb ON p.barcode = sb.product_barcode
        WHERE p.category_id = ?
        GROUP BY p.barcode";
$stmt = $pdo->prepare($sql);
$stmt->execute([$cat_id]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockPilot - <?= htmlspecialchars($category['category_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/specificcategory.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <main class="inventory-wrapper">
        <div class="inventory-header">
            <div class="breadcrumb">
                <span class="category-title"><?= htmlspecialchars($category['category_name']) ?></span>
            </div>
            <div class="inventory-actions">
                <input type="text" placeholder="Search products..." class="search-box-small">
                <button class="addbtn" onclick="location.href='NewOrder.php'">+ Add Item</button>
            </div>
        </div>

        <div class="inventory-card">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Product Name</th>
                        <th>On Hand Stock</th>
                        <th>Avg. Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): 
                        $isLow = $p['current_stock'] <= $p['min_stock_level'];
                    ?>
                    <tr class="<?= $isLow ? 'urgent-row' : '' ?>">
                        <td><strong><?= htmlspecialchars($p['barcode']) ?></strong></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= $p['current_stock'] ?> Units</td>
                        <td>$<?= number_format($p['avg_cost'], 2) ?></td>
                        <td>
                            <?php if($isLow): ?>
                                <span class="status-pill urgent">Low Stock</span>
                            <?php else: ?>
                                <span class="status-pill healthy">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-view-small">View Batches</button>
                            <button class="btn-edit-small">⚙️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>