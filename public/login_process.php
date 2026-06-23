<?php
session_start();
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 🟢 Fixed: Redirect to index.php if fields are empty
    if (empty($username) || empty($password)) {
        header("Location: index.php?error=emptyfields");
        exit();
    }

    $db = Database::getInstance()->getConnection();

    try {
        // Query the employee and pull their role name directly
        $sql = "SELECT e.*, r.role_name 
                FROM employees e 
                JOIN roles r ON e.role_id = r.id 
                WHERE e.username = :username LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 1. Check if user exists and password matches
        // 1. Check if user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            
            // 2. Strict Role Authorization: Matching your exact DB names (Manager / Cashier)
            if ($user['role_name'] === 'Manager' || $user['role_name'] === 'Cashier') {
                
                // Regenerate session ID for security against session fixation
                session_regenerate_id(true);

                // Store critical session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role_name'] = $user['role_name'];

                // 3. Route based on role
                if ($user['role_name'] === 'Manager') {
                    header("Location: MainDashBoard.php");
                } else if ($user['role_name'] === 'Cashier') {
                    header("Location: Cashier.php");
                }
                exit();
            } else {
                // Security Guard & Stock Clerk hit this wall
                header("Location: index.php?error=unauthorized_role");
                exit();
            }
        } else {
            header("Location: index.php?error=invalidcredentials");
            exit();
        }

    } catch (PDOException $e) {
        // 🟢 Fixed: System or database connection failures route back gracefully
        header("Location: index.php?error=systemerror");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}