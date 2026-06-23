<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
// 1. Include the Class file
require_once '../classes/Supplier.php';

// 2. Initialize the Object and Fetch Data
$supplierObj = new Supplier();
$allSuppliers = $supplierObj->getAllSuppliers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - Supplier Directory</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/CSS/header.css">
    <link rel="stylesheet" href="../assets/CSS/maindashboard.css">
    <link rel="stylesheet" href="../assets/CSS/suppliers.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="supplier-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h1>Supplier Directory</h1>
                <p>Manage your vendor relationships and procurement contacts.</p>
            </div>
            <button class="btn-add-supplier" onclick="location.href='RegisterSupplier.php'">
                <span class="icon">+</span> New Supplier Registration
            </button>
        </div>

        <div class="table-container">
           <table class="supplier-table">
    <thead>
        <tr>
            <th>Company Name</th>
            <th>Contact Person</th>
            <th>Categories</th> <!-- 1. Added header column -->
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($allSuppliers)): ?>
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px;"> <!-- Colspan changed to 6 -->
                    No suppliers registered yet.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($allSuppliers as $row): ?>
                <tr>
                    <td>
                        <div class="company-info">
                            <span class="company-name"><?php echo htmlspecialchars($row['supplier_name']); ?></span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                    
                    <!-- 2. Display the concatenated categories here -->
                    <td>
                        <span class="category-badge">
                            <?php echo !empty($row['categories']) ? htmlspecialchars($row['categories']) : 'Uncategorized'; ?>
                        </span>
                    </td>

                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view">📦</button>
                            <button class="btn-edit" onclick="location.href='EditSupplier.php?id=<?php echo $row['id']; ?>'">✏️</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
        </div>
    </main>

</body>
</html>