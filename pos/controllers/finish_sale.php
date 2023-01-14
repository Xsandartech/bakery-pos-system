<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["cart"]) || !isset($_POST["payment_method"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");

$cart = $_POST["cart"];
$payment_method = $_POST["payment_method"];

$pos = new POS();
$result = $pos->finish_sale($cart, $payment_method);

echo json_encode($result);

?>