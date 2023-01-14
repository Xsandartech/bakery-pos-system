<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id"]) || !isset($_POST["description"]) || !isset($_POST["cost"]) || !isset($_POST["price"]) || !isset($_POST["color"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$id = $_POST["id"];
$description = $_POST["description"];
$cost = $_POST["cost"];
$price = $_POST["price"];
$color = $_POST["color"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Product.php");

$Product = new Product();
$result = $Product->update_product($id, $description, $cost, $price, $color);

echo json_encode(array('status' => $result));

?>