<?php
require_once '../classes/Supplier.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Capture the data from the form
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $category_ids = $_POST['category_ids'] ?? []; // This is an array

    // 2. Validation (Basic)
    if (empty($name) || empty($email)) {
        header("Location: ../public/RegisterSupplier.php?error=missing_fields");
        exit();
    }

    // 3. Use the Supplier Class to register
    $supplierObj = new Supplier();
    $success = $supplierObj->registerSupplier($name, $contact, $email, $phone, $category_ids);

    // 4. Redirect based on result
    if ($success) {
        header("Location: ../public/suppliers.php?status=success");
    } else {
        header("Location: ../public/RegisterSupplier.php?error=db_error");
    }
} else {
    // Prevent direct access to this script
    header("Location: ../public/suppliers.php");
}