<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."PrinterSettings.php");

$PrinterSettings = new PrinterSettings();
$settings = $PrinterSettings->get_printer_settings();
echo json_encode($settings);

?>