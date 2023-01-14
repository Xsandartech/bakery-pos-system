<?php

$_POST = json_decode(file_get_contents('php://input'), true);

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Promo.php");


if(!isset($_POST["id"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}
$id = $_POST["id"];

$Promo = new Promo();
$promo_data = $Promo->get_promo_data($id);
echo json_encode($promo_data);
?>