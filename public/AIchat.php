<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockPilot - StockSense AI Command</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/stocksense.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="stocksense-container">
        <aside class="ai-sidebar">
            <div class="sidebar-box">
                <h3><i class="fa-solid fa-database "></i> Live Context</h3>
                <p>StockSense is currently connected to your <strong>Live Database</strong>.</p>
            </div>
            
            <div class="suggested-queries">
                <h4>Suggested Questions</h4>
                <button class="query-chip">"Who is the top cashier today?"</button>
                <button class="query-chip">"Which items expire this week?"</button>
                <button class="query-chip">"What was the total revenue in May?"</button>
                <button class="query-chip">"Give me a summary of stock levels."</button>
            </div>
        </aside>

        <section class="chat-interface shadow-box">
            <div class="chat-header">
                <h2><i class="fa-solid fa-brain" id="brain"></i> StockSense Neural Link</h2>
                <span class="status-indicator">● Online</span>
            </div>

            <div id="chat-stream" class="chat-stream">
                <div class="message ai">
                    <div class="bubble">
                        Hello! I am StockSense. I have analyzed your supermarket's latest data. 
                        How can I help you optimize your business today?
                    </div>
                </div>
            </div>

            <div class="chat-input-area">
                <textarea id="ai-user-input" placeholder="Ask anything about your supermarket..." rows="1"></textarea>
                <button id="send-btn"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </section>
    </main>

    <script src="../assets/js/stocksense-engine.js"></script>
</body>
</html>