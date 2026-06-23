<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
require_once '../classes/Employee.php';
$employeeObj = new Employee();

// FIX: Instead of hardcoding '1', let's find the ID for 'Cashier' dynamically
$db = Database::getInstance()->getConnection();
$roleQuery = $db->query("SELECT id FROM roles WHERE role_name = 'Cashier' LIMIT 1");
$roleRow = $roleQuery->fetch();
$cashierRoleId = $roleRow['id'] ?? 0;

// Fetch only the people who have the Cashier role
$team = $employeeObj->getEmployeesByRoleWithPerformance($cashierRoleId);

// Calculate total for progress bars
$totalDailyRevenue = array_sum(array_column($team, 'total_sales'));

// The winner is the one with sales > 0 at the top
$topCashierId = !empty($team) && $team[0]['total_sales'] > 0 ? $team[0]['id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StockPilot - Cashier Leaderboard</title>
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/CashierEmployee.css">
</head>
<body>

<?php include '../templates/header.php'; ?>

<main class="inventory-wrapper">
  <div class="inventory-header">
    <div>
        <h1>Cashier Performance Leaderboard</h1>
        <p>Real-time sales tracking for today's shift</p>
    </div>
    <div class="total-shop-badge">
        <span>Today's Shop Total:</span>
        <strong>ETB <?= number_format($totalDailyRevenue, 2) ?></strong>
    </div>
  </div>

  <div class="leaderboard-container">
    <?php if (empty($team)): ?>
        <div class="empty-state">No cashiers found in the system.</div>
    <?php else: ?>
        <?php foreach ($team as $index => $emp): 
            $sales = $emp['total_sales'] ?? 0;
            $isWinner = ($emp['id'] === $topCashierId);
            $rank = $index + 1;
        ?>
        <div class="cashier-row <?= $isWinner ? 'winner' : '' ?>">
            <div class="rank-zone">
                <?php if($isWinner): ?>
                    <span class="trophy">🏆</span>
                <?php else: ?>
                    <span class="rank-number">#<?= $rank ?></span>
                <?php endif; ?>
            </div>

            <div class="info-zone">
                <div class="name-status">
                    <strong><?= htmlspecialchars($emp['name']) ?></strong>
                    <span class="status-dot <?= strtolower($emp['status']) ?>"></span>
                </div>
                <small><?= htmlspecialchars($emp['phone']) ?></small>
            </div>

            <div class="performance-zone">
                <div class="sales-bar-container">
                    <div class="sales-label">Revenue: <strong>ETB <?= number_format($sales, 2) ?></strong></div>
                    <div class="progress-bg">
                        <?php 
                        $percent = ($totalDailyRevenue > 0) ? ($sales / $totalDailyRevenue) * 100 : 0;
                        ?>
                        <div class="progress-fill" style="width: <?= $percent ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="action-zone">
                <button class="btn-manage" onclick="location.href='EditEmployee.php?id=<?= $emp['id'] ?>'">
                    <i class="fa-solid fa-user-gear"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>
</body>
</html>