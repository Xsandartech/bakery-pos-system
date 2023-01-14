<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["printer_mode"]) || !isset($_POST["printer_usb_name"]) || !isset($_POST["printer_ip"]) || !isset($_POST["printer_port"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$printer_mode = $_POST["printer_mode"];
$printer_usb_name = $_POST["printer_usb_name"];
$printer_ip = $_POST["printer_ip"];
$printer_port = $_POST["printer_port"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."PrinterSettings.php");

$PrinterSettings = new PrinterSettings();
$result = $PrinterSettings->update_printer_mode($printer_mode, $printer_usb_name, $printer_ip, $printer_port);

echo json_encode(array('status' => $result));

?>