<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id_sale"])) {
    echo "isset";
    exit();
}

$id_sale = $_POST["id_sale"];

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

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }

    public function __toString() {
        return $this->getAsString();
    }
}

$POS = new POS();

$ticket_data = $POS->get_ticket_data($id_sale);

$total_sale = $ticket_data["sale_data"][0]->total;
$total = new item('Total', number_format($total_sale, 2), true);

/* Date */
$date = $ticket_data["sale_data"][0]->datetime;

$items = array();

foreach($ticket_data["items"] as $item) {
    $items[] = new item($item["quantity"]."x ".$item["description"], number_format($item["subtotal"], 2));
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
$printer -> text($date."\n");
$printer -> feed();

/* Items */
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> setEmphasis(true);
//$main_row = new item('Descripción', '$');

$printer -> selectPrintMode(Printer::MODE_UNDERLINE);
$printer -> text(( new item('Descripción', '$'))->getAsString($cols));
$printer -> selectPrintMode();
$printer -> setEmphasis(false);

foreach ($items as $item) {
    $printer->text($item->getAsString($cols));
}

$printer -> text("\n");
/* Total */
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