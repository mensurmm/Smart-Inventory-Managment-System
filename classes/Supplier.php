<?php
require_once 'Database.php';

/**
 * Supplier Class
 * Handles all vendor-related logic for StockPilot.
 */
class Supplier {
    private $db;

    public function __construct() {
        // Obtains the PDO connection from our Singleton Database class
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registers a new supplier into the system.
     * Matches columns from image_1e8b5b.png
     */
/**
 * Registers a new supplier and their multiple categories.
 */
public function registerSupplier($name, $contact, $email, $phone, $category_ids = []) {
    try {
        // Start a transaction so if one part fails, nothing is saved (data integrity)
        $this->db->beginTransaction();

        // 1. Insert the main supplier data
        $sql = "INSERT INTO suppliers (supplier_name, contact_person, email, phone) 
                VALUES (:name, :contact, :email, :phone)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'    => $name,
            ':contact' => $contact,
            ':email'   => $email,
            ':phone'   => $phone
        ]);

        // 2. Get the auto-generated ID for this new supplier
        $supplier_id = $this->db->lastInsertId();

        // 3. Insert the categories into the linking table
        if (!empty($category_ids)) {
            $cat_sql = "INSERT INTO supplier_categories (supplier_id, category_id) VALUES (?, ?)";
            $cat_stmt = $this->db->prepare($cat_sql);
            
            foreach ($category_ids as $cat_id) {
                $cat_stmt->execute([$supplier_id, $cat_id]);
            }
        }

        // 4. Everything worked! Save to database.
        $this->db->commit();
        return true;

    } catch (Exception $e) {
        // Something went wrong, cancel everything
        $this->db->rollBack();
        error_log("Registration Error: " . $e->getMessage());
        return false;
    }
}
 public function getAllSuppliers() {
    $sql = "SELECT s.*, GROUP_CONCAT(c.category_name SEPARATOR ', ') as categories
            FROM suppliers s
            LEFT JOIN supplier_categories sc ON s.id = sc.supplier_id
            LEFT JOIN categories c ON sc.category_id = c.id
            GROUP BY s.id
            ORDER BY s.supplier_name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /**
     * Relationship Method: Gets all products associated with a specific supplier.
     * Uses the stock_batches table as the link (image_1e8f92.png).
     */
    public function getProductsBySupplier($supplier_id) {
        $sql = "SELECT DISTINCT p.name, p.sku 
                FROM products p
                JOIN stock_batches b ON p.id = b.product_id
                WHERE b.supplier_id = :s_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':s_id' => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}