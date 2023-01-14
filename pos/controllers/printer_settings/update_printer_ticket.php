<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if( !isset($_POST["printer_cols"]) || !isset($_POST["ticket_title"]) || !isset($_POST["ticket_subtitle"]) || !isset($_POST["ticket_footer"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$printer_cols = $_POST["printer_cols"];
$ticket_title = $_POST["ticket_title"];
$ticket_subtitle = $_POST["ticket_subtitle"];
$ticket_footer = $_POST["ticket_footer"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."PrinterSettings.php");

$PrinterSettings = new PrinterSettings();
$result = $PrinterSettings->update_printer_ticket($printer_cols, $ticket_title, $ticket_subtitle, $ticket_footer);

echo json_encode(array('status' => $result));

?>