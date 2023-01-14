<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class Product {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_products() {
        $query = $this->pdo->prepare("SELECT * FROM pos_products WHERE visible = 1 ORDER BY description ASC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function insert_product($description, $cost, $price, $color) {
        $query = $this->pdo->prepare("INSERT INTO pos_products (description, cost, price, color) VALUES (?, ?, ?, ?)");
        $result = $query->execute([$description, $cost, $price, $color]);

        if ($result) return "ok";
            else return "error";
    }

    public function delete_product($id) {
        $query = $this->pdo->prepare("UPDATE pos_products SET visible = ? WHERE id = ?");
        $result = $query->execute([0, $id]);

        if ($result == true) return "ok";
            return "error";
    }

    public function update_product($id, $description, $cost, $price, $color) {
        $query = $this->pdo->prepare("UPDATE pos_products SET description = ?, cost = ?, price = ?, color = ? WHERE id = ?");
        $result = $query->execute([$description, $cost, $price, $color, $id]);

        if ($result === true) return "ok";
            else return "error";
    }

    
} 

?>