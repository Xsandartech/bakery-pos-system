<?php

$_POST = json_decode(file_get_contents('php://input'), true);


if(!isset($_POST["date"]) || !isset($_POST["work_shift"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$date = $_POST["date"];
$work_shift = $_POST["work_shift"];

/*$work_shift = 0;*/

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."Report.php");
$Report = new Report();

$work_shifts = $Report->get_work_shifts_by_day($date);

//get total sales, expenses and withdrawal
$expenses = array();

$withdrawals = array();

$incomes = array();

$remaining_money = 0;
$missing_money = 0;

switch(sizeof($work_shifts)) {
    case 0:
        $expenses[] = 0;
        $withdrawals[] = 0;
        $incomes[] = 0;
    break;

    case 1:
        if ($work_shift === 0 ) {

            $since_date = $work_shifts[0]->datetime;
            $to_date = $work_shifts[0]->finished_at;

            if ($to_date == "") { //the work shift is not over yet
                $time = strtotime($since_date);
                $newformat = date('Y-m-d 23:59:59', $time);
                $to_date = $newformat;
            }

            $expenses[] = $Report->get_cash_drawer_movements($since_date,  $to_date, 0);
            $withdrawals[] = $Report->get_cash_drawer_movements($since_date, $to_date, 1);
            $incomes[] = $Report->get_total_incomes($since_date, $to_date);
    
            $real_difference = $work_shifts[0]->expected_money - $work_shifts[0]->final_money;
            $difference = abs($real_difference);
    
            if ($real_difference < 0) $missing_money = $difference;
                elseif ($real_difference > 0) $remaining_money = $difference;
        }

    break;

    case 2:
        $expenses[] = $Report->get_cash_drawer_movements($work_shifts[$work_shift]->datetime, $work_shifts[$work_shift]->finished_at, 0);
        $withdrawals[] = $Report->get_cash_drawer_movements($work_shifts[$work_shift]->datetime, $work_shifts[$work_shift]->finished_at, 1);
        $incomes[] = $Report->get_total_incomes($work_shifts[$work_shift]->datetime, $work_shifts[$work_shift]->finished_at);

        $real_difference = number_format($work_shifts[$work_shift]->final_money - $work_shifts[$work_shift]->expected_money, 2);
        $difference = abs($real_difference);

        if ($real_difference < 0) $missing_money = $difference;
            elseif ($real_difference > 0) $remaining_money = $difference;

    break;

  }

  //make report :)
  $report = array();
  $report["remaining_money"] = number_format($remaining_money, 2);
  $report["missing_money"] = number_format($missing_money, 2);
  $report["expenses"] = $expenses;
  $report["withdrawals"] = $withdrawals;
  $report["incomes"] = $incomes;

  echo json_encode($report);

?>