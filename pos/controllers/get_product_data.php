<?php

$_POST = json_decode(file_get_contents('php://input'), true);

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");


if(!isset($_POST["id"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}
$id = $_POST["id"];

$POS = new POS();
$product_data = $POS->get_product_data($id);
echo json_encode($product_data);
?>