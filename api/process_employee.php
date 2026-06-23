<?php
require_once '../classes/Employee.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeObj = new Employee();

    // Collect and sanitize input
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $role_id = $_POST['role_id'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($username) || empty($password)) {
        header("Location: ../public/RegisterEmployees.php?error=emptyfields");
        exit();
    }

    // Attempt to add the employee
    $result = $employeeObj->addEmployee($name, $role_id, $username, $password, $phone);

    if ($result) {
        // Success! Redirect back to the main management hub
        header("Location: ../public/ManageEmployees.php?success=registered");
    } else {
        // Failure (e.g., username already exists)
        header("Location: ../public/RegisterEmployees.php?error=sqlerror");
    }
    exit();
} else {
    // If someone tries to access this file directly without posting
    header("Location: ../public/ManageEmployees.php");
    exit();
}