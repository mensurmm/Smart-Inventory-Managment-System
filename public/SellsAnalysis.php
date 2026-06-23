<?php
require_once '../templates/auth_check.php';
restrictToRoles(['Manager']);
?>
<?php
require_once '../classes/Database.php';
$db = Database::getInstance()->getConnection();

// --- 1. CALCULATE REVENUE TOTALS ---
// Lifetime
$totalRevenue = $db->query("SELECT SUM(total_price) FROM sales_log")->fetchColumn() ?? 0;
// Today
$dailyRevenue = $db->query("SELECT SUM(total_price) FROM sales_log WHERE DATE(sale_date) = CURDATE()")->fetchColumn() ?? 0;
// Average
$avgTransaction = $db->query("SELECT AVG(total_price) FROM sales_log")->fetchColumn() ?? 0;

// --- 2. WEEKLY DATA (Last 7 Days) ---
$weeklyLabels = [];
$weeklyData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weeklyLabels[] = date('D', strtotime("-$i days"));
    $stmt = $db->prepare("SELECT SUM(total_price) FROM sales_log WHERE DATE(sale_date) = ?");
    $stmt->execute([$date]);
    $weeklyData[] = $stmt->fetchColumn() ?? 0;
}

// --- 3. YEARLY DATA (Current Year) ---
$yearlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$yearlyData = array_fill(0, 12, 0);
$stmtYear = $db->query("SELECT MONTH(sale_date) as month, SUM(total_price) as total FROM sales_log WHERE YEAR(sale_date) = YEAR(CURDATE()) GROUP BY MONTH(sale_date)");
while ($row = $stmtYear->fetch(PDO::FETCH_ASSOC)) {
    $yearlyData[$row['month'] - 1] = $row['total'];
}

// --- 4. TOP PRODUCT (Weekly) ---
$topProductQuery = $db->query("
    SELECT p.name, SUM(s.quantity_sold) as total_units 
    FROM sales_log s 
    JOIN products p ON s.product_barcode = p.barcode 
    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY s.product_barcode 
    ORDER BY total_units DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$topProductName = $topProductQuery['name'] ?? "No Sales";
$topProductUnits = $topProductQuery['total_units'] ?? 0;

// --- 🤖 5. REAL-TIME AI RECOMMENDATION ENGINE ---

// CARD 1: Dynamic Velocity Alerts
$velocityAlert = "<strong>Stock Velocity Status:</strong> Retail turnover values are running stable across all inventory channels. No critical depletions detected.";
$vStmt = $db->query("
    SELECT p.name, p.quantity, SUM(s.quantity_sold) as weekly_sold
    FROM sales_log s
    JOIN products p ON s.product_barcode = p.barcode
    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY s.product_barcode
    HAVING p.quantity < (weekly_sold * 0.5) OR p.quantity <= 10
    ORDER BY weekly_sold DESC LIMIT 1
");
if ($velocityItem = $vStmt->fetch(PDO::FETCH_ASSOC)) {
    $velocityAlert = "<strong>Stock Velocity Alert:</strong> '" . htmlspecialchars($velocityItem['name']) . "' is selling fast (" . $velocityItem['weekly_sold'] . " sold this week). Remaining stock (" . $velocityItem['quantity'] . ") will deplete soon.";
}

// CARD 2: Dynamic Expiry Risks
$expiryAlert = "<strong>Waste Prevention:</strong> No immediate product expiration items found inside the active 14-day tracking window.";
$eStmt = $db->query("
    SELECT p.name, b.expiry_date, DATEDIFF(b.expiry_date, CURDATE()) as days_left 
    FROM stock_batches b
    JOIN products p ON b.product_barcode = p.barcode
    WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
    ORDER BY b.expiry_date ASC LIMIT 1
");
if ($expiryItem = $eStmt->fetch(PDO::FETCH_ASSOC)) {
    $days = $expiryItem['days_left'];
    $timeText = ($days === 0) ? "expires today" : ($days === 1 ? "expires tomorrow" : "expires in $days days");
    $expiryAlert = "<strong>Waste Prevention:</strong> A batch of '" . htmlspecialchars($expiryItem['name']) . "' $timeText (" . date('M d, Y', strtotime($expiryItem['expiry_date'])) . ").";
}

// CARD 3: Data-Driven Cross-Selling Bundle Suggestion
$bundleAlert = "<strong>Bundle Suggestion:</strong> Pair fast-moving snacks with high-margin beverages at terminal checkout lane points.";
$bStmt = $db->query("
    SELECT p1.name as item1, p2.name as item2 
    FROM sales_log s1
    JOIN sales_log s2 ON s1.sale_date = s2.sale_date AND s1.product_barcode < s2.product_barcode
    JOIN products p1 ON s1.product_barcode = p1.barcode
    JOIN products p2 ON s2.product_barcode = p2.barcode
    GROUP BY s1.product_barcode, s2.product_barcode
    ORDER BY COUNT(*) DESC LIMIT 1
");
if ($bundlePair = $bStmt->fetch(PDO::FETCH_ASSOC)) {
    $bundleAlert = "<strong>Bundle Suggestion:</strong> Customers frequently purchase '" . htmlspecialchars($bundlePair['item1']) . "' and '" . htmlspecialchars($bundlePair['item2']) . "' together. Try a promo bundle.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - Sales Analysis & AI Insights</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/analysis.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="analysis-container">
        <header class="analysis-header">
            <div>
                <h1>Sales Analytics</h1>
                <p>Deep dive into your supermarket's performance</p>
            </div>
            <div class="time-toggle">
                <button class="btn-toggle">Daily</button>
                <button class="btn-toggle active">Weekly</button>
                <button class="btn-toggle">Yearly</button>
            </div>
        </header>

        <section class="metrics-grid">
            <div class="metric-card">
                <div class="card-info">
                    <h3 id="revenue-title">Total Revenue</h3>
                    <span class="value" id="revenue-value">ETB <?= number_format($totalRevenue, 2) ?></span>
                    <span class="trend up"><i class="fa-solid fa-arrow-up"></i> Live Performance</span>
                </div>
                <i class="fa-solid fa-sack-dollar icon" id="sack"></i>
            </div>

            <div class="metric-card">
                <div class="card-info">
                    <h3>Top Product (Weekly)</h3>
                    <span class="value"><?= htmlspecialchars($topProductName) ?></span>
                    <span class="sub-text"><?= $topProductUnits ?> units sold</span>
                </div>
                <i class="fa-solid fa-crown icon" id="crown"></i>
            </div>

            <div class="metric-card">
                <div class="card-info">
                    <h3>Average Transaction</h3>
                    <span class="value">ETB <?= number_format($avgTransaction, 2) ?></span>
                    <span class="trend info">Across all logs</span>
                </div>
                <i class="fa-solid fa-receipt icon" id="logs"></i>
            </div>
        </section>

        <section class="charts-row">
            <div class="chart-box shadow-box">
                <h3 id="chart-title">Weekly Sales Distribution</h3>
                <div class="canvas-holder">
                    <canvas id="weeklyBarChart"></canvas>
                </div>
            </div>
            <div class="chart-box shadow-box">
                <h3>Yearly Growth Trend</h3>
                <div class="canvas-holder">
                    <canvas id="yearlyLineChart"></canvas>
                </div>
            </div>
        </section>

        <section class="ai-action-center shadow-box">
            <div class="ai-title">
                <i class="fa-solid fa-robot"></i>
                <h2>AI Smart Pilot Recommendations</h2>
            </div>
            
            <div class="recommendation-grid">
                <div class="ai-card">
                    <div class="ai-tag high-priority">Inventory Insight</div>
                    <p><?= $velocityAlert ?></p>
                    <a href="NewOrder.php" class="ai-btn">Order from Supplier</a>
                </div>
                
                <div class="ai-card">
                    <div class="ai-tag warning-priority">Expiry Risk</div>
                    <p><?= $expiryAlert ?></p>
                    <a href="#" class="ai-btn secondary" onclick="alert('Clearance sale initialized for fast checkout allocation.'); return false;">Create Clearance Sale</a>
                </div>
                
                <div class="ai-card">
                    <div class="ai-tag info-priority">Strategic Move</div>
                    <p><?= $bundleAlert ?></p>
                    <a href="Inventory.php" class="ai-btn">View Inventory</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Data from PHP
        const weeklyLabels = <?= json_encode($weeklyLabels) ?>;
        const weeklyData = <?= json_encode($weeklyData) ?>;
        const yearlyLabels = <?= json_encode($yearlyLabels) ?>;
        const yearlyData = <?= json_encode($yearlyData) ?>;
        
        const statsData = {
            daily: {
                revenue: "ETB <?= number_format($dailyRevenue, 2) ?>",
                title: "Today's Revenue",
                chartTitle: "Today's Sales (Hourly Estimate)",
                chartData: [0, 0, 0, 0, 0, 0, <?= $dailyRevenue ?>],
                labels: weeklyLabels
            },
            weekly: {
                revenue: "ETB <?= number_format(array_sum($weeklyData), 2) ?>",
                title: "Weekly Revenue",
                chartTitle: "Weekly Sales Distribution",
                chartData: weeklyData,
                labels: weeklyLabels
            },
            yearly: {
                revenue: "ETB <?= number_format(array_sum($yearlyData), 2) ?>",
                title: "Annual Revenue",
                chartTitle: "Yearly Growth Trend",
                chartData: yearlyData,
                labels: yearlyLabels
            }
        };
    </script>
    <script src="../assets/js/sales-analysis.js"></script>
    
    <script>
        // Switcher Logic
        const btns = document.querySelectorAll('.btn-toggle');
        const revValue = document.getElementById('revenue-value');
        const revTitle = document.getElementById('revenue-title');
        const chartTitle = document.getElementById('chart-title');

        btns.forEach(btn => {
            btn.addEventListener('click', function() {
                btns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const mode = this.innerText.toLowerCase();
                const data = statsData[mode];

                // Update Text Metrics
                revValue.innerText = data.revenue;
                revTitle.innerText = data.title;
                chartTitle.innerText = data.chartTitle;

                // 1. Update the Bar Chart (Main Display)
                const barChart = Chart.getChart("weeklyBarChart");
                if (barChart) {
                    barChart.data.labels = data.labels;
                    barChart.data.datasets[0].data = data.chartData;
                    barChart.update();
                }

                // 2. Update the Line Chart (Trend Display)
                const lineChart = Chart.getChart("yearlyLineChart");
                if (lineChart) {
                    lineChart.data.labels = data.labels;
                    lineChart.data.datasets[0].data = data.chartData;
                    lineChart.data.datasets[0].label = mode.charAt(0).toUpperCase() + mode.slice(1) + " Trend";
                    lineChart.update();
                }
            });
        });
    </script>
</body>
</html>