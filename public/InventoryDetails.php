<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php 
require_once '../classes/Database.php';
$pdo = Database::getInstance()->getConnection();

$sql = "SELECT c.*, 
        COUNT(p.barcode) as product_count, 
        IFNULL(SUM(sb.quantity), 0) as total_units
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        LEFT JOIN stock_batches sb ON p.barcode = sb.product_barcode
        GROUP BY c.id";
$categories = $pdo->query($sql)->fetchAll();

function getCategoryIcon($name) {
    $name = strtolower($name);
    $icons = [
        'dairy'      => 'fa-cheese',
        'bakery'     => 'fa-bread-slice',
        'beverages'  => 'fa-bottle-water',
        'produce'    => 'fa-apple-whole',
        'meat'       => 'fa-drumstick-bite',
        'frozen'     => 'fa-snowflake',
        'snacks'     => 'fa-cookie-bite',
        'household'  => 'fa-soap'
    ];
    foreach ($icons as $key => $icon) {
        if (strpos($name, $key) !== false) {
            return $icon;
        }
    }
    return 'fa-box';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockPilot - Category Explorer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/detailedinventory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <main class="inventory-wrapper">
        <div class="inventory-header">
            <h1>Category Explorer</h1>
            <p>Browse supermarket categories with real-time stock insights.</p>
        </div>

        <div class="category-grid">
            <?php foreach($categories as $cat): 
                $iconClass = getCategoryIcon($cat['category_name']);
            ?>
            <div class="category-card">
                <div class="category-image">
                    <i class="fa-solid <?= $iconClass ?>" style="color: #3498db;"></i>
                </div> 
                <h3><?= htmlspecialchars($cat['category_name']) ?></h3>
                <div class="category-stats">
                    <span><strong><?= $cat['product_count'] ?></strong> Products</span>
                    <span><strong><?= number_format($cat['total_units']) ?></strong> Units</span>
                </div>
                <button class="btn-outline" onclick="location.href='specificcategory.php?id=<?= $cat['id'] ?>'">
                    View Items
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
