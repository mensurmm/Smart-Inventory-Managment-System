<?php
require_once '../templates/auth_check.php';
// Restrict to your two active roles
restrictToRoles(['Manager', 'Cashier']);

require_once '../classes/Database.php';

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

// Pull fresh account details directly from the database
try {
    $sql = "SELECT e.*, r.role_name 
            FROM employees e 
            JOIN roles r ON e.role_id = r.id 
            WHERE e.id = :id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $userId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $account = null;
}

// Fallback in case account records fail to bind
$name = htmlspecialchars($account['name'] ?? $_SESSION['user_name'] ?? 'Employee');
$role = htmlspecialchars($account['role_name'] ?? $_SESSION['role_name'] ?? 'Staff');
$username = htmlspecialchars($account['username'] ?? 'N/A');
$phone = htmlspecialchars($account['phone'] ?? 'N/A');
$status = htmlspecialchars($account['status'] ?? 'Active');
$initial = strtoupper(substr($name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - Account Details</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="profile-container">
        <div class="profile-card">
            
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar"><?= $initial ?></div>
                <span class="status-badge <?= strtolower($status) ?>"><?= $status ?></span>
            </div>

            <div class="profile-meta-header">
                <h2><?= $name ?></h2>
                <p class="role-tag <?= strtolower($role) ?>"><i class="fa-solid fa-shield-halved"></i> <?= $role ?></p>
            </div>

            <hr class="profile-divider">

            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label"><i class="fa-solid fa-user"></i> Username</span>
                    <span class="detail-value">@<?= $username ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fa-solid fa-phone"></i> Phone Number</span>
                    <span class="detail-value"><?= $phone ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fa-solid fa-fingerprint"></i> System ID</span>
                    <span class="detail-value">#EMP-<?= str_pad($userId, 4, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fa-solid fa-clock"></i> Session Status</span>
                    <span class="detail-value continuous">Connected</span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="MainDashBoard.php" class="btn-dashboard"><i class="fa-solid fa-house"></i> Return to Portal</a>
            </div>

        </div>
    </main>

</body>
</html>