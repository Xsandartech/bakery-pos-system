
<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class SafeBox {
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
  
        $query = $this->pdo->prepare("INSERT INTO safe_box_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
        $query_update_safe_box = $this->pdo->prepare("UPDATE safe_box SET current_money = current_money - ? WHERE id = ?");

        try {
            $this->pdo->beginTransaction();

            $query->execute([$now, $resp, 1, $description, $amount]);
            $query_update_safe_box->execute([$amount, 1]);

            $result = $this->pdo->commit();

            if($result === true) return "ok";
                else return "error";
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
	        die($e->getMessage());
            return "error";
        }
    }

    public function get_movements_history($datetime) {
        $query = $this->pdo->prepare("SELECT DATE_FORMAT(safe_box_movements.datetime, '%h:%i:%s%p') AS datetime, users.display_name AS resp, safe_box_movements.type,
        safe_box_movements.description, safe_box_movements.amount FROM safe_box_movements INNER JOIN users ON safe_box_movements.resp = users.id
        WHERE safe_box_movements.datetime >= ? ORDER BY safe_box_movements.id DESC");
        $query->execute([$datetime]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function get_current_money(){
        $query = $this->pdo->prepare("SELECT current_money FROM safe_box WHERE id = 1");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $current_money = $result->current_money;
        return $current_money;
    }
}

?>