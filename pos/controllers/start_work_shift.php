<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["starting_money"])) {
    echo json_encode(array('result' => 'isset'));
    exit();
}

$starting_money = $_POST["starting_money"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."WorkShift.php");

$work_shift = new WorkShift();

$result = $work_shift->start_work_shift($starting_money);
echo json_encode(array('result' => $result));

?>