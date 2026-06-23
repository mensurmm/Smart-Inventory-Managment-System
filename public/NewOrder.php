<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php 
require_once '../classes/Product.php';
require_once '../classes/Database.php';

// Initialize Database and get the actual PDO connection
$databaseInstance = Database::getInstance();
$pdo = $databaseInstance->getConnection(); 

// Fetch dynamic data for dropdowns
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - New Stock Entry</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/CSS/header.css">
    <link rel="stylesheet" href="../assets/CSS/Neworder.css">
    
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        /* Modern Styles for the Integrated Scanner Section */
        .scanner-card-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #edf2f7;
        }
        .scanner-header-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .scanner-title-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #reader-container {
            width: 100%;
            max-width: 480px;
            margin: 15px auto 0 auto;
            border-radius: 12px;
            overflow: hidden;
            background: #111827;
            border: 2px solid #0f6fff;
            display: none; /* Controlled programmatically by JS engine */
        }
        .btn-action-scan {
            background-color: #0f6fff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-action-scan:hover {
            opacity: 0.9;
        }
        /* Visual flash feedback when a scan succeeds */
        @keyframes flashSuccess {
            0% { backgroundColor: #ffffff; }
            50% { backgroundColor: #e8f5e9; borderColor: #10b981; }
            100% { backgroundColor: #ffffff; }
        }
        .input-success-flash {
            animation: flashSuccess 1.2s ease-in-out;
        }
    </style>
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="register-wrapper">
        <div class="register-header">
            <h1>New Stock Entry</h1>
            <p>Register a new product or add a new batch to existing stock.</p>
        </div>

        <div class="scanner-card-wrapper">
            <div class="scanner-header-block">
                <div class="scanner-title-text">
                    <i class="fa-solid fa-barcode" style="color: #0f6fff;"></i>
                    <span>Hardware Integration Panel</span>
                </div>
                <button type="button" id="btn-scan" class="btn-action-scan">
                    <i class="fa-solid fa-camera"></i> Toggle Camera
                </button>
            </div>
            
            <div id="reader-container">
                <div id="reader"></div>
            </div>
        </div>

        <form action="../api/process_order.php" method="POST" class="register-form">
            <div class="form-grid">
                
                <div class="register-card">
                    <div class="card-icon">🆔</div>
                    <h3>Product Identity</h3>
                    <div class="input-group">
                        <label>Barcode</label>
                        <input type="text" id="barcode-target-input" name="barcode" placeholder="Scan Barcode or Type Manually" required>
                    </div>
                    <div class="input-group">
                        <label>Product Name</label>
                        <input type="text" name="name" placeholder="e.g., Coca Cola 500ml" required>
                    </div>
                    <div class="input-group">
                        <label>Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" placeholder="0.00" required>
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="register-card">
                    <div class="card-icon">📦</div>
                    <h3>Batch Details</h3>
                    <div class="form-row">
                        <div class="input-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" placeholder="0" required>
                        </div>
                        <div class="input-group">
                            <label>Cost per Unit</label>
                            <input type="number" step="0.01" name="cost_price" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Supplier</label>
                       <select name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="input-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary" id="btn-form-reset">Clear Form</button>
                <button type="submit" class="btn-primary">Complete Entry</button>
            </div>
        </form>
    </main>

    <script>
    let isScanning = false;
    const html5QrCode = new Html5Qrcode("reader");

    const barcodeTargetInput = document.getElementById("barcode-target-input");
    const readerContainer = document.getElementById("reader-container");
    const btnScan = document.getElementById("btn-scan");
    const btnReset = document.getElementById("btn-form-reset");

    // --- 1. SCANNER ENGINE CONFIGURATION ---
    const scanConfig = { fps: 15, qrbox: { width: 250, height: 150 } };

    btnScan.addEventListener("click", () => {
        if (!isScanning) {
            // Unveil viewport container element so core canvas can extract dimensional metrics
            readerContainer.style.display = "block";

            html5QrCode.start(
                { facingMode: "environment" }, // Access standard rear-facing camera stream
                scanConfig,
                (text) => {
                    // 🎯 BARCODE CAPTURE SUCCESSFUL
                    processDecodedBarcode(text);
                    
                    // Pause engine tracking execution loop to preserve system processing resources
                    html5QrCode.pause();
                    
                    // Auto-resume camera processing thread after 2.5 seconds cooling interval
                    setTimeout(() => { 
                        if (isScanning) html5QrCode.resume(); 
                    }, 2500);
                }
            )
            .then(() => {
                isScanning = true;
                btnScan.innerHTML = '<i class="fa-solid fa-camera-slash"></i> Stop Camera';
                btnScan.style.backgroundColor = "#e74c3c"; // Crimson alert theme warning active video draw
            })
            .catch((err) => {
                readerContainer.style.display = "none";
                console.error("Camera Initialization Stack Failure Error:", err);
                alert("Camera initialization failed: " + err);
            });
        } else {
            turnOffCamera();
        }
    });

    // --- 2. BARCODE PROCESSING PIPELINE ---
    function processDecodedBarcode(barcodeString) {
        if (!barcodeString) return;

        // Clean white spaces and strings from input
        const cleanCode = barcodeString.trim();
        
        // Inject data payload string programmatically into target input box element
        barcodeTargetInput.value = cleanCode;
        
        // Push keyboard input focus context to target field container
        barcodeTargetInput.focus();

        // Trigger dynamic CSS success UI animations to visually notify manager
        barcodeTargetInput.classList.add("input-success-flash");
        setTimeout(() => {
            barcodeTargetInput.classList.remove("input-success-flash");
        }, 1200);
    }

    // --- 3. SAFE ENGINE DISCOVERY CLOSURE HANDLERS ---
    function turnOffCamera() {
        if (isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                readerContainer.style.display = "none";
                btnScan.innerHTML = '<i class="fa-solid fa-camera"></i> Toggle Camera';
                btnScan.style.backgroundColor = ""; // Reset inline override configuration to original blue state
            }).catch(err => console.error("Safe video pipeline shutdown error track:", err));
        }
    }

    // Turn camera loop off gracefully if manager triggers a manual form reset action
    btnReset.addEventListener("click", () => {
        turnOffCamera();
    });
    </script>
</body>
</html>