<?php
require_once '../templates/auth_check.php';
// Allows both Managers and Cashiers to view the system specs
restrictToRoles(['Manager', 'Cashier']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - About Us</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/about.css">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <main class="about-container">
        <header class="about-header">
            <h1>About StockPilot</h1>
            <p>Smart Inventory. Smarter Decisions.</p>
        </header>

        <p class="project-intro">
            StockPilot is an all-in-one supermarket management solution engineered to bridge the gap between daily retail operations and data-driven business insights. Built specifically to handle modern retail environments, the platform unifies fast-paced Point of Sale (POS) environments with secure back-end management tools. By translating raw transaction logs and stock levels into real-time operational feedback, StockPilot eliminates paperwork, maximizes inventory turnover efficiency, and empowers managers to make smart decisions ahead of time.
        </p>

        <section class="features-grid">
            <div class="feature-card">
                <i class="fa-solid fa-boxes-stacked feature-icon"></i>
                <h3>Real-Time Inventory Control</h3>
                <p>Track warehouse assets, check stock depletion thresholds dynamically, and log newly arrived supplier batches seamlessly with absolute precision.</p>
            </div>

            <div class="feature-card">
                <i class="fa-solid fa-chart-line feature-icon"></i>
                <h3>Sales Analytics & Performance</h3>
                <p>Process interactive sales tracking telemetry data instantly. Monitor transaction logs, view daily revenue flow, and track active staff performance metrics panels.</p>
            </div>

            <div class="feature-card">
                <i class="fa-solid fa-robot feature-icon"></i>
                <h3>StockSense AI Recommendations</h3>
                <p>Utilize algorithmic modeling engines to forecast product velocity curves, catch immediate product expiration risks, and generate stock replenishment logs safely.</p>
            </div>
        </section>

        <section class="dev-section">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>System Development</h2>
            <p>StockPilot was fully conceptualized, designed, and engineered by Information System students from <strong>Addis Ababa University</strong> as a modern digital response to commercial supermarket operational optimization.</p>
        </section>
    </main>

</body>
</html>