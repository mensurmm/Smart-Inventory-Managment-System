<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>StockPilot - Register Employee</title>
    <link rel="stylesheet" href="../assets/CSS/style.css" />
    <link rel="stylesheet" href="../assets/CSS/newemployee.css" />
    <link rel="stylesheet" href="../assets/css/header.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>
<body>
    <?php 
    include '../templates/header.php'; 
    require_once '../classes/Employee.php';
    $employeeObj = new Employee();
    $roles = $employeeObj->getEmployeeStats(); 
    ?>

    <main class="register-wrapper">
        <div class="register-header">
            <h1>Register New Staff</h1>
            <p>Assign a role and create system credentials for a new employee.</p>
            
            <!-- Success/Error Feedback -->
            <?php if(isset($_GET['error'])): ?>
                <p style="color: #e74c3c; font-weight: bold;">Registration failed. Please check your details.</p>
            <?php endif; ?>
        </div>

        <form action="../api/process_employee.php" method="POST" class="register-form">
            <div class="form-grid">
                <!-- Personal Details Card -->
                <div class="register-card">
                    <div class="card-icon">👤</div>
                    <h3>Personal Details</h3>
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="Enter full name" required />
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="e.g. +251..." required />
                    </div>
                    <div class="input-group">
                        <label>Assign Role</label>
                        <select name="role_id" required>
                            <option value="" disabled selected>Select a role...</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- System Credentials Card -->
                <div class="register-card">
                    <div class="card-icon">🔑</div>
                    <h3>System Credentials</h3>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Choose a username" required />
                    </div>
                    <div class="input-group">
                        <label>Initial Password</label>
                        <input type="password" name="password" placeholder="Create a password" required />
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn-primary">Register Employee</button>
            </div>
        </form>
    </main>
</body>
</html>