<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id"])) {
    echo "isset";
    exit();
}

$id = $_POST["id"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Promo.php");

$Promo = new Promo();

$result = $Promo->delete_promo($id);

echo json_encode(array('status' => $result));

?>