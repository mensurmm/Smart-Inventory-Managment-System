<?php
// 1. 🟢 Upgraded to use your central authentication middleware
require_once '../templates/auth_check.php';

// 2. 🟢 Restrict this screen explicitly to the Cashier role
restrictToRoles(['Cashier']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>StockPilot - Cashier Desk</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/cashier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
    <?php include '../templates/Cashierheader.php'; ?>

    <header class="cashier-subheader">
        <div class="cashier-info">
            <span><i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <span class="status-indicator">● Online</span>
        </div>
    </header>

    <main class="cashier-container">
        <section class="cart-section">
            <div class="scanner-wrapper">
                <div class="scanner-controls">
                    <button id="btn-scan" class="btn-primary">
                        <i class="fa-solid fa-camera"></i> Toggle Camera
                    </button>
                    <div class="manual-input-group">
                        <input type="text" id="manual_barcode" placeholder="Manual Entry...">
                        <button id="btn-manual-search" class="btn">Add</button>
                    </div>
                </div>

                <div id="reader-container" style="display:none; margin-top:15px; border-radius:12px; overflow:hidden; border:2px solid #3498db; background:#000;">
                    <div id="reader" style="width:100%;"></div>
                </div>
            </div>

            <div class="cart-display">
                <table id="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body"></tbody>
                </table>
            </div>
        </section>

        <aside class="checkout-aside">
            <div class="total-card">
                <h3>Total Amount</h3>
                <div class="amount-display">ETB <span id="grand-total">0.00</span></div>
                
                <div class="summary-line"><span>Subtotal</span><span id="subtotal">0.00</span></div>
                <div class="summary-line"><span>Tax (15%)</span><span id="tax">0.00</span></div>

                <div class="payment-method">
                    <label>Payment Mode</label>
                    <div class="method-grid">
                        <button class="method-btn active" data-mode="Cash">Cash</button>
                        <button class="method-btn" data-mode="Telebirr">Telebirr</button>
                        <button class="method-btn" data-mode="Card">Card</button>
                    </div>
                </div>

                <button id="btn-complete" class="btn-finish-sale">Complete Transaction</button>
            </div>
        </aside>
    </main>

    <script src="../assets/js/cashier_logic.js"></script>
</body>
</html>