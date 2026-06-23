<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php 
require_once '../classes/Employee.php';
$employeeObj = new Employee();
$stats = $employeeObj->getEmployeeStats();
$topCashier = $employeeObj->getTopCashier(); // Fetch the star performer

$icons = [
    'Cashier' => '💰',
    'Stock Clerk' => '📦',
    'Security Guard' => '🛡️',
    'Management' => '👔'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StockPilot - Staff Management</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/maindashboard.css">
  <link rel="stylesheet" href="../assets/css/manageemployees.css">
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include '../templates/header.php'; ?>

  <main class="inventory-wrapper">
    <div class="inventory-header staff-header">
      <h1>Staff Management Hub</h1>
      <button class="btn" onclick="location.href='RegisterEmployees.php'">+ Add New Employee</button>
    </div>

   <div class="staff-grid">
    <?php if (empty($stats)): ?>
        <p>No roles found in the database.</p>
    <?php else: ?>
        <?php foreach ($stats as $role): ?>
            <div class="staff-card">
                <div class="staff-icon"><?php echo $icons[$role['role_name']] ?? '👤'; ?></div>
                <h3><?php echo htmlspecialchars($role['role_name']); ?>s</h3>
                <p><strong><?php echo $role['total']; ?></strong> Active Staff</p>

                <?php if ($role['role_name'] === 'Cashier' && $topCashier): ?>
                <?php endif; ?>

                <button class="btn-outline" onclick="location.href='<?php 
                    echo ($role['role_name'] === 'Cashier') 
                        ? "CashierEmployee.php" 
                        : "DetailedEmployees.php?role_id=" . $role['role_id']; 
                ?>'">
                    View Team
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
  </main>
</body>
</html>