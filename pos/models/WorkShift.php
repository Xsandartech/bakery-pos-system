<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class WorkShift {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function check_work_shift() { //check if a work shift is open
        $query = $this->pdo->prepare("SELECT * FROM work_shift ORDER BY id DESC LIMIT 1");
            $query->execute();

            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result) return $result;
                else  return "empty";
        
    }

    public function start_work_shift($starting_money) {
        date_default_timezone_set("America/Mexico_City");
        $now = date("Y-m-d H:i:s");

        session_start();
        $user_id = $_SESSION['user_id'];

        //open work shift
        $query = $this->pdo->prepare("INSERT INTO work_shift (datetime, starting_money, started_by) VALUES (?, ?, ?)");
        $result = $query->execute([$now, $starting_money, $user_id]);

        if($result === true) return "ok";
            else return "error";
    }

    public function finish_work_shift($expected_money, $final_money, $work_shift_id, $finished_at) {
        /*date_default_timezone_set("America/Mexico_City");
        $now = date("Y-m-d H:i:s");*/

        $real_difference = floatval($final_money - $expected_money);
        $difference = abs($real_difference);

        session_start();
        $user_id = $_SESSION['user_id'];

        try {
            $this->pdo->beginTransaction();

            if ($real_difference < 0) { //Missing money!
                $querydiff = $this->pdo->prepare("INSERT INTO cash_drawer_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
                $querydiff->execute([$finished_at, $user_id, 3, "Efectivo faltante al cerrar turno.", $difference]);

            } elseif ($real_difference > 0) { //Remaining money
                $querydiff = $this->pdo->prepare("INSERT INTO cash_drawer_movements (datetime, resp, type, description, amount) VALUES (?, ?, ?, ?, ?)");
                $querydiff->execute([$finished_at, $user_id, 2, "Efectivo sobrante al cerrar turno.", $difference]);
            }

            $query = $this->pdo->prepare("UPDATE work_shift SET expected_money = ?, final_money = ?, finished_by = ?, is_finished = ?, finished_at = ? WHERE id = ?");
            $query->execute([$expected_money, $final_money, $user_id, 1, $finished_at, $work_shift_id]);

            $result = $this->pdo->commit();

            if($result === true) return "ok";
                else return "error";

        } catch (\PDOException $e) {
	        $this->pdo->rollBack();
	        die($e->getMessage());
            return "error";
        }
    }
}

/*$test = new WorkShift();
print_r($test->check_work_shift());
*/

?>