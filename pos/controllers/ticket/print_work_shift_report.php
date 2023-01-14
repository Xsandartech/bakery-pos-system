<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["work_shift_report"])) {
    echo "isset";
    exit();
}

$work_shift_report = $_POST["work_shift_report"];


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
$printer -> text("BREAD FACTORY\n");
$printer -> selectPrintMode();
$printer -> text("Cierre de turno\n");
$printer -> feed();

$printer -> setJustification(Printer::JUSTIFY_LEFT);


$printer -> text("Turno del día ".$work_shift_report["date"]."\n");

$printer -> text("Iniciado a las " . $work_shift_report["started_at"]."\n");
$printer -> text("Finalizado a las " . $work_shift_report["finished_at"]);
$printer -> feed();

$printer -> setEmphasis(true);
//$main_row = new item('Descripción', '$');
$printer -> text(( new item('', '$'))->getAsString($cols));
$printer -> selectPrintMode();
$printer -> setEmphasis(false);

$printer -> text((new item('Fondo de caja', $work_shift_report["starting_money"]))->getAsString($cols));
$printer -> text((new item('Ventas totales', $work_shift_report["total_sales"]))->getAsString($cols));
$printer -> text((new item('Gastos', $work_shift_report["total_expenses"]))->getAsString($cols));
$printer -> text((new item('Retiros', $work_shift_report["total_withdrawals"]))->getAsString($cols));

$printer -> text("\n");

$printer -> text((new item('Efectivo esperado', $work_shift_report["expected_money"]))->getAsString($cols));
$printer -> text((new item('Efectivo final', $work_shift_report["final_money"]))->getAsString($cols));
$printer -> text((new item('Faltante', $work_shift_report["missing_money"]))->getAsString($cols));
$printer -> text((new item('Sobrante', $work_shift_report["remaining_money"]))->getAsString($cols));

/* Cut the receipt and open the cash drawer */
$printer -> cut();
$printer -> pulse();

$printer -> close();

echo json_encode(array("status" => "ok"));
?>