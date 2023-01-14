<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["description"]) || !isset($_POST["amount"]) || !isset($_POST["movement_type"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."CashDrawer.php");

$description = $_POST["description"];
$amount = $_POST["amount"];
$movement_type = $_POST["movement_type"];

$cash_drawer = new CashDrawer();

switch($movement_type) {
    case "expense": 
        $result = $cash_drawer->register_expense($description, $amount);
    break;

    case "withdrawal":
        $result = $cash_drawer->register_withdrawal($description, $amount);
    break;
}

echo json_encode($result);
?>