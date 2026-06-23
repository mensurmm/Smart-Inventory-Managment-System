<?php
class Batch {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // This handles the "1 Batch" count you mentioned
    public function createBatch($product_id, $supplier_id, $quantity, $arrival_date, $expiry_date) {
        $query = "INSERT INTO stock_batches (product_id, supplier_id, quantity, arrival_date, expiry_date) 
                  VALUES (:p_id, :s_id, :qty, :arr, :exp)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'p_id' => $product_id,
            's_id' => $supplier_id,
            'qty'  => $quantity,
            'arr'  => $arrival_date,
            'exp'  => $expiry_date
        ]);
    }
}