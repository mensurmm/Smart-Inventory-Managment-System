<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php 
// Set a page title for the template
$pageTitle = "StockPilot - Register Supplier"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/regsupplier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="register-wrapper">
        <div class="register-header">
            <h1>New Supplier Registration</h1>
            <p>Add a new vendor to your network to streamline procurement and inventory restocking.</p>
        </div>

        <form action="../api/process_supplier.php" method="POST" class="register-form">
            <div class="form-grid">
                
                <!-- Section 1: Company Profile -->
                <div class="register-card">
                    <div class="card-icon">🏢</div>
                    <h3>Company Profile</h3>
                    <div class="input-group">
                        <label>Company Name</label>
                        <input type="text" name="name" placeholder="e.g., Abyssinia General Trading" required>
                    </div>

                    <!-- Dynamic Category Selection -->
                    <div class="input-group">
                        <label>Business Categories</label>
                        <select name="category_ids[]" multiple required style="height: 120px; width: 100%; border-radius: 6px; border: 1px solid #ddd; padding: 10px;">
                            <?php
                            try {
                                require_once '../classes/Database.php';
                                $db = Database::getInstance()->getConnection();
                                $stmt = $db->query("SELECT id, category_name FROM categories ORDER BY category_name ASC");
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['category_name']) . "</option>";
                                }
                            } catch (Exception $e) {
                                echo "<option disabled>Error loading categories</option>";
                            }
                            ?>
                        </select>
                        <small style="color: #666; margin-top: 5px; display: block;">
                            <i class="fas fa-info-circle"></i> Hold <strong>Ctrl</strong> (Win) or <strong>Command</strong> (Mac) to select multiple.
                        </small>
                    </div>

                    <div class="input-group">
                        <label>Office Address</label>
                        <input type="text" name="address" placeholder="Bole, Addis Ababa, Ethiopia" required>
                    </div>
                </div>

                <!-- Section 2: Contact Information -->
                <div class="register-card">
                    <div class="card-icon">📞</div>
                    <h3>Primary Contact Details</h3>
                    <div class="input-group">
                        <label>Contact Person Name</label>
                        <input type="text" name="contact_person" placeholder="Full Name" required>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" placeholder="+251..." required>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="vendor@example.com" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Notes / Logistics Info</label>
                        <textarea name="notes" placeholder="Delivery schedules, lead times, etc." style="width: 100%; border-radius: 6px; border: 1px solid #ddd; padding: 10px; height: 80px;"></textarea>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.history.back();">Cancel</button>
                <button type="submit" class="btn-primary">Save Supplier Profile</button>
            </div>
        </form>
    </main>

</body>
</html>