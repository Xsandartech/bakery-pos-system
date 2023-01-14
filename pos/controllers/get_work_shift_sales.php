<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");
include (MODELS_PATH."WorkShift.php");

$POS = new POS();
$WorkShift = new WorkShift();

//get work shift opening date 
$current_workshift = $WorkShift->check_work_shift();
$datetime = $current_workshift->datetime;

$sales = $POS->get_sales($datetime);

echo json_encode($sales);

?>