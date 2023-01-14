<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."WorkShift.php");
include (MODELS_PATH."SafeBox.php");

$SafeBox = new SafeBox();
$WorkShift = new WorkShift();

//get work shift opening date 
$current_workshift = $WorkShift->check_work_shift();
$datetime = $current_workshift->datetime;

$movements = $SafeBox->get_movements_history($datetime);

echo json_encode($movements);

?>