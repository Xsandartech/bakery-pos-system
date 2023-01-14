<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id_pos_product"]) || !isset($_POST["description"]) || !isset($_POST["pieces"]) || !isset($_POST["price"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$id_pos_product = $_POST["id_pos_product"];
$description = $_POST["description"];
$pieces = $_POST["pieces"];
$price = $_POST["price"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Promo.php");

$Promo = new Promo();
$result = $Promo->insert_promo($id_pos_product, $description, $pieces, $price);

echo json_encode(array('status' => $result));

?>