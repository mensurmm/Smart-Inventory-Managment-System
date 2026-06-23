<?php
require_once 'Database.php';

class Employee {
    private $db;

    public function __construct() {
        // Fix: Get the PDO connection from the Singleton instance
        $this->db = Database::getInstance()->getConnection();
    }

    public function getEmployeeStats() {
        // Now $this->db is the actual PDO object, so prepare() will work
        $sql = "SELECT r.id as role_id, r.role_name, COUNT(e.id) as total 
                FROM roles r 
                LEFT JOIN employees e ON r.id = e.role_id 
                GROUP BY r.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getTopCashier() {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT e.name, SUM(s.total_price) as revenue 
            FROM sales_log s 
            JOIN employees e ON s.cashier_id = e.id 
            GROUP BY s.cashier_id 
            ORDER BY revenue DESC LIMIT 1";
    return $db->query($sql)->fetch(PDO::FETCH_ASSOC);
}
public function getEmployeesByRoleWithPerformance($role_id) {
    $db = Database::getInstance()->getConnection();
    // Use a JOIN to roles to be 100% sure we only get the right category
    $sql = "SELECT e.*, 
            (SELECT SUM(total_price) FROM sales_log s 
             WHERE s.cashier_id = e.id AND DATE(s.sale_date) = CURDATE()) as total_sales
            FROM employees e
            WHERE e.role_id = ? 
            ORDER BY total_sales DESC, e.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$role_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  public function addEmployee($name, $role_id, $username, $password, $phone) {
    try {
        // Securely hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO employees (name, role_id, username, password, phone, status) 
                VALUES (:name, :role_id, :username, :password, :phone, 'active')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'     => $name,
            ':role_id'  => $role_id,
            ':username' => $username,
            ':password' => $hashedPassword,
            ':phone'    => $phone
        ]);
    } catch (PDOException $e) {
        // This will catch things like duplicate usernames if you have a UNIQUE constraint
        return false;
    }
}
}