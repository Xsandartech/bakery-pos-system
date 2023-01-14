<?php

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");

require __DIR__ . '/../../../libs/escpos-php/autoload.php';
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;

/* Start the printer */
/* SET PRINTER MODE */
include (MODELS_PATH."PrinterSettings.php");
$PrinterSettings = new PrinterSettings();
$settings = $PrinterSettings->get_printer_settings();
$mode = $settings->printer_mode;
$ticket_title = $settings->ticket_title;
$ticket_subtitle = $settings->ticket_subtitle;
$ticket_footer = $settings->ticket_footer;
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
$profile = CapabilityProfile::load("POS-5890");
$printer = new Printer($connector, $profile);
$cols = $settings->printer_cols;

/* A wrapper to do organise item names & prices into columns */
class item {
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false) {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function getAsString($width = 48) {
        $rightCols = 9;
        $leftCols = $width - $rightCols;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }

    public function __toString() {
        return $this->getAsString();
    }
}

/* Print top logo */
try{
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	$logo = EscposImage::load("ticket_logo.png", false);
    $printer->bitImage($logo);
}catch(Exception $e){}

/* Header */
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
$printer -> text($ticket_title."\n");
$printer -> selectPrintMode();
$printer -> text($ticket_subtitle."\n");
$printer -> feed();

$printer -> setEmphasis(true);
//$main_row = new item('Descripción', '$');
$printer -> text(( new item('', '$'))->getAsString($cols));
$printer -> selectPrintMode();
$printer -> setEmphasis(false);

$printer -> text((new item('Testing printer', 9999.99))->getAsString($cols));

/* Total */
$printer -> text("\n");
$total = new item('Total',9999.99, true);
$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
$printer -> setJustification(Printer::JUSTIFY_RIGHT);
$printer -> text($total->getAsString($cols));
$printer -> selectPrintMode();

/* Footer */
$printer -> feed(1);
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text($ticket_footer."\n");

/* Cut the receipt and open the cash drawer */
$printer -> cut();
$printer -> pulse();

$printer -> close();

echo json_encode(array("status" => "ok"));
?>