<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["final_money"])) {
    echo json_encode(array('result' => 'isset'));
    exit();
}

$final_money = $_POST["final_money"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."WorkShift.php");
include (MODELS_PATH."POS.php");
include (MODELS_PATH."CashDrawer.php");

//Get work shift data
$WorkShift = new WorkShift();
$work_shift = $WorkShift->check_work_shift();
$work_shift_id = $work_shift->id;
$work_shift_dt = $work_shift->datetime;

/*
* Calculate expected money :)
*/
$expected_money = 0;

//Starting money
$starting_money = $work_shift->starting_money;

//Total sales
$POS = new POS();
$sales = $POS->get_sales($work_shift_dt);
$total_sales = 0;
foreach($sales as $sale) {
    $type = $sale->payment_method;
    $type = (int) $type;

    if ($type === 0) $total_sales += $sale->total;
}

//Cash drawer movements
$CashDrawer = new CashDrawer();
$cash_drawer_movements = $CashDrawer->get_movements_history($work_shift_dt);

$total_expenses = 0;
$total_withdrawals = 0;

foreach($cash_drawer_movements as $movement) {
    $type = $movement->type;
    switch($type) {
        case 0: case 1://Expenses
            $total_expenses += $movement->amount;
        break;

        case 1: //Withdrawals
            $total_withdrawals += $movement->amount;
        break;
    }
}

$expected_money = $starting_money + $total_sales - $total_expenses - $total_withdrawals;

date_default_timezone_set("America/Mexico_City");
$now = date("Y-m-d H:i:s");

$result = $WorkShift->finish_work_shift($expected_money, $final_money, $work_shift_id, $now);

if ($result === "ok") {
    $old_date = date($work_shift_dt);  
    $old_date_timestamp = strtotime($old_date);
    $new_date = date('d/m/Y', $old_date_timestamp);
    $started_at = date('h:i:s A', $old_date_timestamp);

    $old_date = date($now);  
    $old_date_timestamp = strtotime($old_date);
    $finished_at = date('h:i:s A', $old_date_timestamp);

    //Print ticket report :)
    $report = new stdClass();
    $report->date = $new_date;
    $report->started_at = $started_at;
    $report->finished_at = $finished_at;

    $report->starting_money = number_format($starting_money, 2);
    $report->total_sales = number_format($total_sales, 2);
    $report->total_expenses = number_format($total_expenses, 2);
    $report->total_withdrawals = number_format($total_withdrawals, 2);
    $report->expected_money = number_format($expected_money, 2);
    $report->final_money = number_format($final_money, 2);

    $real_difference = floatval($final_money - $expected_money);
    $difference = abs($real_difference);

    $missing_money = 0;
    $remaining_money = 0;

    if ($real_difference < 0) $missing_money = $difference;
        elseif ($real_difference > 0) $remaining_money = $difference;

    $report->missing_money = number_format($missing_money, 2);
    $report->remaining_money = number_format($remaining_money, 2);

    echo json_encode(array('result' => 'ok', 'report' => $report));

} else echo json_encode(array('result' => $result));

?>