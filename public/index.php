<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockPilot - Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <header class="main-header">
    <div class="header-container">
        <div class="logo">
            <h1>Stock<span>Pilot</span></h1>
        </div>
        <nav class="nav-links">
            <a href="contact.php" class="contact-link">Contact Us</a>
        </nav>
    </div>
</header>

<div class="login-container">
    <div class="brand-header">
        <h2>StockPilot</h2>
        <p>Smart Inventory Management</p>
    </div>
    
    <form action="login_process.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter your username">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn-login">Access Dashboard</button>
    </form>
   <p>“Smart Inventory. Smarter Decisions.”</p> 
</div>

</body>
</html>