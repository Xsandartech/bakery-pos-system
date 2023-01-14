<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include_once (DB_PATH."connection.php");

class Report {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_total_incomes($since_date, $to_date) {

        if ($to_date == "") { //the work shift is not over yet
            $time = strtotime($since_date);
            $newformat = date('Y-m-d 23:59:59', $time);
            $to_date = $newformat;
        }

        $total_income_query = $this->pdo->prepare("SELECT SUM(total) FROM pos_sales WHERE datetime BETWEEN ? AND ?");

        $total_income_query->execute([$since_date, $to_date]);

        $total = $total_income_query->fetch(PDO::FETCH_NUM);

        return $total[0];
    }

    public function get_cash_drawer_movements($since_date, $to_date, $type) {
        /*
        cash_drawer type 
            0 -> expense
            1 -> withdrawal
            2 -> remaining money
            3 -> missing money
        */

        $query = $this->pdo->prepare("SELECT DATE_FORMAT(cash_drawer_movements.datetime, '%h:%i:%s%p') AS datetime, users.display_name AS resp,
        cash_drawer_movements.description, cash_drawer_movements.amount FROM cash_drawer_movements INNER JOIN users ON cash_drawer_movements.resp = users.id
        WHERE (cash_drawer_movements.datetime BETWEEN ? AND ?) AND cash_drawer_movements.type = ? ORDER BY cash_drawer_movements.id DESC");
        $query->execute([$since_date, $to_date, $type]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /*
    public function get_withdrawals($since_date, $to_date) {
        $query = $this->pdo->prepare("SELECT DATE_FORMAT(cash_drawer_movements.datetime, '%h:%i:%s%p') AS datetime, users.display_name AS resp,
        cash_drawer_movements.description, cash_drawer_movements.amount FROM cash_drawer_movements INNER JOIN users ON cash_drawer_movements.resp = users.id
        WHERE (cash_drawer_movements.datetime BETWEEN ? AND ?) AND (cash_drawer_movements.type = ?) ORDER BY cash_drawer_movements.id DESC");
        $query->execute([$since_date, $to_date, 1]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }*/

    public function get_total_expenses($since_date, $to_date) {

        if ($to_date == "") { //the work shift is not over yet
            $time = strtotime($since_date);
            $newformat = date('Y-m-d 23:59:59', $time);
            $to_date = $newformat;
        }

        $total_expense_query = $this->pdo->prepare("SELECT SUM(amount) FROM cash_drawer_movements WHERE (datetime BETWEEN ? AND ?) AND type = ?");

        $total_expense_query->execute([$since_date, $to_date, 0]);

        $total = $total_expense_query->fetch(PDO::FETCH_NUM);

        return $total[0];
    }

    public function get_work_shifts_by_day($date) { //get open workshifts by day
        $query = $this->pdo->prepare("SELECT * FROM work_shift WHERE datetime BETWEEN ? AND ? ORDER BY id ASC");
            $query->execute([$date." 00:00:00", $date." 23:59:59"]);

            $result = $query->fetchAll(PDO::FETCH_OBJ);
            return $result;
    }
}

/*$Report = new Report();
print_r($Report->get_total_expenses("2022-03-18 00:00:00", "2022-03-18 23:59:59"));
*/

?>

