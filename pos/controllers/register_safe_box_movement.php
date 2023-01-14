<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["description"]) || !isset($_POST["amount"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."SafeBox.php");

$description = $_POST["description"];
$amount = $_POST["amount"];

/*$SafeBox = new SafeBox();
$current_money = $SafeBox->get_current_money();

if ($current_money < $amount) echo json_encode(array('result' => 'insufficient_money'));
else {
    $result = $SafeBox->register_expense($description, $amount);
    echo json_encode(array('result' => $result));
}
*/

$result = $SafeBox->register_expense($description, $amount);

echo json_encode(array('result' => $result));
?>