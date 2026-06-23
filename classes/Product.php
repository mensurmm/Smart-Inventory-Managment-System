<?php
require_once 'Database.php';

class Product {
    private $db;

    public function __construct() {
        // Get the actual PDO connection from your Singleton instance
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Register or update a product using the Barcode as the primary identifier.
     */
    public function register($barcode, $name, $category_id, $selling_price = 0.00) {
        $query = "INSERT INTO products (barcode, name, category_id, selling_price, created_at) 
                  VALUES (:barcode, :name, :category_id, :selling_price, NOW())
                  ON DUPLICATE KEY UPDATE 
                  name = :name_update, 
                  category_id = :cat_update, 
                  selling_price = :price_update";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':barcode'       => $barcode,
            ':name'          => $name,
            ':category_id'   => $category_id,
            ':selling_price' => $selling_price,
            ':name_update'   => $name,
            ':cat_update'    => $category_id,
            ':price_update'  => $selling_price
        ]);
    }

    /**
     * Add a new stock batch and update the live quantity in the products table.
     */
    public function addBatch($barcode, $supplier_id, $quantity, $cost_price, $arrival_date, $expiry_date) {
        try {
            // Start transaction to ensure both steps happen together
            $this->db->beginTransaction();

            // 1. Insert into stock_batches
            $query_batch = "INSERT INTO stock_batches (product_barcode, supplier_id, quantity, cost_price, arrival_date, expiry_date) 
                            VALUES (:barcode, :supplier_id, :quantity, :cost_price, :arrival_date, :expiry_date)";
            
            $stmt_batch = $this->db->prepare($query_batch);
            $stmt_batch->execute([
                ':barcode'      => $barcode,
                ':supplier_id'  => $supplier_id,
                ':quantity'     => $quantity,
                ':cost_price'   => $cost_price,
                ':arrival_date' => $arrival_date,
                ':expiry_date'  => $expiry_date
            ]);

            // 2. Update the total live quantity in the products table
            // This ensures the Cashier Screen sees the new stock immediately
            $query_update = "UPDATE products SET quantity = quantity + :new_qty WHERE barcode = :barcode";
            $stmt_update = $this->db->prepare($query_update);
            $stmt_update->execute([
                ':new_qty' => $quantity,
                ':barcode' => $barcode
            ]);

            // Commit the changes
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback if anything fails
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Stock Entry Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get details of a product by barcode for auto-filling forms.
     */
    public function getDetails($barcode) {
        $query = "SELECT * FROM products WHERE barcode = :barcode";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':barcode' => $barcode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}