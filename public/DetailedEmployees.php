<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
require_once '../classes/Employee.php';
$employeeObj = new Employee();

$role_id = $_GET['role_id'] ?? null;

if (!$role_id) {
    header("Location: ManageEmployees.php");
    exit();
}

$team = $employeeObj->getEmployeesByRoleWithPerformance($role_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StockPilot - Employee Directory</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/detailedemployees.css">
</head>
<body>

<?php include '../templates/header.php'; ?>

<main class="inventory-wrapper">
  <div class="inventory-header">
    <div class="breadcrumb">
      <span class="category-title">Staff Directory</span>
    </div>
  </div>

  <div class="inventory-card">
    <table class="inventory-table">
  <thead>
    <tr>
      <th>Employee Name</th>
      <th>Phone Number</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($team)): ?>
      <tr>
        <td colspan="4" style="text-align:center; padding:20px;">No staff found for this role.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($team as $emp): ?>
      <tr class="<?php echo (isset($emp['status']) && $emp['status'] !== 'active') ? 'offline-row' : ''; ?>">
        <td><strong><?php echo htmlspecialchars($emp['name']); ?></strong></td>
        <td><?php echo htmlspecialchars($emp['phone']); ?></td>
        <td>
          <span class="status-<?php echo strtolower($emp['status'] ?? 'active'); ?>">
            ● <?php echo ucfirst($emp['status'] ?? 'active'); ?>
          </span>
        </td>
        <td>
          <button class="btn-view-small">Edit Profile</button>
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