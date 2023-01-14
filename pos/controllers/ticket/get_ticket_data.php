<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id_sale"])) {
    echo "isset";
    exit();
}

$id_sale = $_POST["id_sale"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");

$POS = new POS();
$ticket_data = $POS->get_ticket_data($id_sale);

echo json_encode($ticket_data);

?>