<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function restrictToRoles(array $allowedRoles) {
    // If not logged in, kick back to login page
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_name'])) {
        header("Location: index.php");
        exit();
    }

    // Convert all allowed roles to lowercase for an exact, bulletproof match
    $allowedRolesLower = array_map('strtolower', $allowedRoles);
    $userRoleLower = strtolower($_SESSION['role_name']);

    // Check if the user's role is in the allowed list
    if (!in_array($userRoleLower, $allowedRolesLower)) {
        // Cashiers get sent back to their own page if they try to access management zones
        if ($userRoleLower === 'cashier') {
            header("Location: Cashier.php?error=access_denied");
        } else {
            // Everyone else gets kicked out to index.php
            header("Location: index.php?error=access_denied");
        }
        exit();
    }
}