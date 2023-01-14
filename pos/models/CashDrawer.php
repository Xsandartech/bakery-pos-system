<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class CashDrawer {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function register_expense($description, $amount) {
        date_default_timezone_set("America/Mexico_City");

        $now = date("Y-m-d H:i:s");

        session_start();

        $resp = $_SESSION['user_id'];
  
        $query = $this->pdo->prepare("INSERT INTO cash_drawer_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
        $result = $query->execute([$now, $resp, 0, $description, $amount]);

        if($result === true) return "ok";
            else return "error";
    }

    public function register_withdrawal($description, $amount) {
        /*
            cash_drawer type 
            0 -> expense
            1 -> withdrawal
            2 -> remaining money
            3 -> missing money

            safe_box type
            0 -> income, 1 -> expense
        */
        date_default_timezone_set("America/Mexico_City");

        $now = date("Y-m-d H:i:s");

        session_start();

        $resp = $_SESSION['user_id'];

        $query = $this->pdo->prepare("INSERT INTO cash_drawer_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
        $result = $query->execute([$now, $resp, 1, $description, $amount]);

        if($result === true) return "ok";
            else return "error";
            
        /*
        $query_cash_drawer = $this->pdo->prepare("INSERT INTO cash_drawer_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
        $query_safe_box = $this->pdo->prepare("INSERT INTO safe_box_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
        $query_update_safe_box = $this->pdo->prepare("UPDATE safe_box SET current_money = current_money + ? WHERE id = ?");
        
        try {
            $this->pdo->beginTransaction();

            $query_cash_drawer->execute([$now, $resp, 1, $description, $amount]);
            $query_safe_box->execute([$now, $resp, 0, $description, $amount]);
            $query_update_safe_box->execute([$amount, 1]);

            $result = $this->pdo->commit();

            if($result === true) return "ok";
            else return "error";

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
	        die($e->getMessage());
            return "error";
        }*/

    }

    public function get_movements_history($datetime) {
        $query = $this->pdo->prepare("SELECT DATE_FORMAT(cash_drawer_movements.datetime, '%h:%i:%s%p') AS datetime, users.display_name AS resp, cash_drawer_movements.type,
        cash_drawer_movements.description, cash_drawer_movements.amount FROM cash_drawer_movements INNER JOIN users ON cash_drawer_movements.resp = users.id
        WHERE cash_drawer_movements.datetime >= ? ORDER BY cash_drawer_movements.id DESC");
        $query->execute([$datetime]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}

?>