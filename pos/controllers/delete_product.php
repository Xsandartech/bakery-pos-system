<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id"])) {
    echo "isset";
    exit();
}

$id = $_POST["id"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Product.php");

$Product = new Product();

$result = $Product->delete_product($id);

echo json_encode(array('status' => $result));

?>