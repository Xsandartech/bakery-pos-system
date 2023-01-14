<?php

/* SET PRINTER MODE */
include (MODELS_PATH."PrinterSettings.php");
$PrinterSettings = new PrinterSettings();
$settings = $PrinterSettings->get_printer_settings();
$mode = $settings->printer_mode;
$connector = null;
if ($mode === "0") {
    //USB
    $printer_name = $settings->printer_usb_name;
    $connector = new WindowsPrintConnector($printer_name);
} elseif($mode === "1") {
    //IP
    $ip = $settings->printer_ip;
    $port = $settings->printer_port;
    $connector = new NetworkPrintConnector($ip, $port);
}
/* ****************** */

?>