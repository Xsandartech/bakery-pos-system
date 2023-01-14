<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class Promo {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_promos() {
        $query = $this->pdo->prepare("SELECT pos_promos.id, pos_promos.description, pos_promos.pieces, pos_promos.price, pos_products.description AS product 
        FROM pos_promos INNER JOIN pos_products ON pos_promos.id_pos_product = pos_products.id WHERE pos_promos.visible = 1 ORDER BY description ASC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function insert_promo($id_pos_product, $description, $pieces, $price) {
        $query = $this->pdo->prepare("INSERT INTO pos_promos (id_pos_product, description, pieces, price) VALUES (?, ?, ?, ?)");
        $result = $query->execute([$id_pos_product, $description, $pieces, $price]);

        if ($result) return "ok";
            else return "error";
    }

    public function delete_promo($id) {
        $query = $this->pdo->prepare("UPDATE pos_promos SET visible = ? WHERE id = ?");
        $result = $query->execute([0, $id]);

        if ($result == true) return "ok";
            return "error";
    }

    public function update_promo($id, $id_pos_product, $description, $pieces, $price) {
        $query = $this->pdo->prepare("UPDATE pos_promos SET id_pos_product = ?, description = ?, pieces = ?, price = ? WHERE id = ?");
        $result = $query->execute([$id_pos_product, $description, $pieces, $price, $id]);

        if ($result === true) return "ok";
            else return "error";
    }

    public function get_promo_data($id) {
        $query = $this->pdo->prepare("SELECT * from pos_promos WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    

    
} 

?>