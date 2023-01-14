<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["week"]) || !isset($_POST["year"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$week = $_POST["week"];
$year = $_POST["year"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."Report.php");
$Report = new Report();

date_default_timezone_set("America/Mexico_City");

$now = date("Y-m-d H:i:s");

/*$week = date("W"); //week number
$year = date("Y");*/

function getWeekDates($week, $year) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret[0] = $dto->format('Y-m-d'); //monday
    for ($i = 1; $i < 7; $i++) {
        $dto->modify('+1 days');
        $ret[$i] = $dto->format('Y-m-d');
    }

    return $ret;
  }
  
//get week dates
$week_array = getWeekDates($week, $year);

$time = strtotime($week_array[0]);
$first_date_week  = date('d/m/Y', $time);
$time = strtotime($week_array[6]);
$last_date_week  = date('d/m/Y', $time);

  //get total incomes and expenses by work shift
  $total_incomes_1 = array();
  $total_incomes_2 = array();

  $total_expenses_1 = array();
  $total_expenses_2 = array();

  for ($i = 0; $i < 7; $i++) {
      $work_shifts = $Report->get_work_shifts_by_day($week_array[$i]);
      
      switch(sizeof($work_shifts)) {
        case 0:
            $total_incomes_1[] = 0;
            $total_incomes_2[]= 0;

            $total_expenses_1[] = 0;
            $total_expenses_2[] = 0;
        break;

        case 1:
            $total_incomes_1[] = $Report->get_total_incomes($work_shifts[0]->datetime, $work_shifts[0]->finished_at);
            $total_incomes_2[] = 0;

            $total_expenses_1[] = $Report->get_total_expenses($work_shifts[0]->datetime, $work_shifts[0]->finished_at);
            $total_expenses_2[] = 0;
        break;

        case 2:
            $total_incomes_1[] = $Report->get_total_incomes($work_shifts[0]->datetime, $work_shifts[0]->finished_at);
            $total_incomes_2[] = $Report->get_total_incomes($work_shifts[1]->datetime, $work_shifts[1]->finished_at);

            $total_expenses_1[] = $Report->get_total_expenses($work_shifts[0]->datetime, $work_shifts[0]->finished_at);
            $total_expenses_2[] = $Report->get_total_expenses($work_shifts[1]->datetime, $work_shifts[1]->finished_at);

        break;

      }
  }

  echo json_encode(array("result" => "ok",
      "incomes_1" => $total_incomes_1, "incomes_2" => $total_incomes_2,
      "expenses_1" => $total_expenses_1, "expenses_2" => $total_expenses_2,
      "first_date_week" => $first_date_week, "last_date_week" => $last_date_week));

?>