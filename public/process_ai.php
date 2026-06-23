<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/Database.php';
require_once '../classes/AIService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$inputData = json_decode(file_get_contents('php://input'), true);
$userMessage = $inputData['message'] ?? '';

if (empty(trim($userMessage))) {
    echo json_encode(['reply' => 'Please type something so I can help you!']);
    exit();
}

$db = Database::getInstance()->getConnection();
$context = [];

// --- 1. SYSTEM CHRONOLOGY ---
$context['system_metadata'] = [
    'current_date' => date('Y-m-d'),
    'current_time' => date('H:i:s'),
    'day_of_week'  => date('l')
];

// --- 2. LIFETIME & PERIOD REVENUE METRICS ---
$context['financial_overview'] = [
    'lifetime_total_revenue' => $db->query("SELECT SUM(total_price) FROM sales_log")->fetchColumn() ?? 0,
    'lifetime_total_transactions' => $db->query("SELECT COUNT(*) FROM sales_log")->fetchColumn() ?? 0,
    'today_revenue' => $db->query("SELECT SUM(total_price) FROM sales_log WHERE DATE(sale_date) = CURDATE()")->fetchColumn() ?? 0,
    'average_transaction_value' => $db->query("SELECT AVG(total_price) FROM sales_log")->fetchColumn() ?? 0
];

// --- 3. INVENTORY & BATCH STATUS SUMMARY ---
$context['inventory_summary'] = [
    'total_distinct_items' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0,
    'total_stock_units_on_shelves' => $db->query("SELECT SUM(quantity) FROM products")->fetchColumn() ?? 0,
    'critical_low_stock_alerts' => $db->query("SELECT name, quantity FROM products WHERE quantity <= 5 ORDER BY quantity ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
    'expiring_batches_within_7_days' => $db->query("SELECT COUNT(*) FROM stock_batches WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetchColumn() ?? 0
];

// --- 4. STAFFING BREAKDOWNS & PERFORMANCE ---
// Overall structure
$context['staff_distribution'] = [
    'total_employees_registered' => $db->query("SELECT COUNT(*) FROM employees")->fetchColumn() ?? 0,
    'active_status_count' => $db->query("SELECT COUNT(*) FROM employees WHERE status='Active'")->fetchColumn() ?? 0,
    'role_breakdown' => $db->query("SELECT r.role_name, COUNT(e.id) as staff_count FROM employees e JOIN roles r ON e.role_id = r.id GROUP BY e.role_id")->fetchAll(PDO::FETCH_ASSOC)
];

// Top Cashier Today
$topCashierToday = $db->query("
    SELECT e.name, SUM(s.total_price) as revenue 
    FROM sales_log s 
    JOIN employees e ON s.cashier_id = e.id 
    WHERE DATE(s.sale_date) = CURDATE()
    GROUP BY s.cashier_id ORDER BY revenue DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
$context['staff_performance']['top_cashier_today'] = $topCashierToday ? $topCashierToday['name'] . " (ETB " . number_format($topCashierToday['revenue'], 2) . ")" : "No transactions today";

// Lifetime Top Cashier
$topCashierLifetime = $db->query("
    SELECT e.name, SUM(s.total_price) as revenue 
    FROM sales_log s 
    JOIN employees e ON s.cashier_id = e.id 
    GROUP BY s.cashier_id ORDER BY revenue DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
$context['staff_performance']['top_cashier_all_time'] = $topCashierLifetime ? $topCashierLifetime['name'] . " (ETB " . number_format($topCashierLifetime['revenue'], 2) . " total)" : "No historical logs";

// --- 5. ITEM VELOCITY TRENDS (PRODUCT SCORES) ---
// Weekly Winner
$topProductWeekly = $db->query("
    SELECT p.name, SUM(s.quantity_sold) as units 
    FROM sales_log s 
    JOIN products p ON s.product_barcode = p.barcode 
    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY s.product_barcode ORDER BY units DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
$context['product_trends']['top_product_weekly'] = $topProductWeekly ? $topProductWeekly['name'] . " (" . $topProductWeekly['units'] . " units)" : "None";

// Lifetime Winner
$topProductLifetime = $db->query("
    SELECT p.name, SUM(s.quantity_sold) as units 
    FROM sales_log s 
    JOIN products p ON s.product_barcode = p.barcode 
    GROUP BY s.product_barcode ORDER BY units DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
$context['product_trends']['top_product_all_time'] = $topProductLifetime ? $topProductLifetime['name'] . " (" . $topProductLifetime['units'] . " units total)" : "None";

// --- 6. HISTORICAL REVENUE COMPARISONS (Last 3 Months) ---
$context['historical_monthly_performance'] = $db->query("
    SELECT DATE_FORMAT(sale_date, '%M %Y') as billing_period, SUM(total_price) as gross_total 
    FROM sales_log 
    WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
    GROUP BY YEAR(sale_date), MONTH(sale_date)
    ORDER BY YEAR(sale_date) DESC, MONTH(sale_date) DESC
")->fetchAll(PDO::FETCH_ASSOC);


// Execute Request via Service Container Pipeline
$aiService = new AIService();
$aiReply = $aiService->askStockSense($userMessage, $context);

echo json_encode(['reply' => $aiReply]);