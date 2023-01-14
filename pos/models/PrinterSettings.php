<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class PrinterSettings {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_printer_settings() {
        $query = $this->pdo->prepare("SELECT * FROM printer_settings WHERE id = ?");
        $query->execute([1]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result[0];
    }

    public function update_printer_mode($printer_mode, $printer_usb_name, $printer_ip, $printer_port) {
        $query = $this->pdo->prepare("UPDATE printer_settings SET printer_mode = ?, printer_usb_name = ?, printer_ip = ?, printer_port = ? WHERE id = ?");
        $result = $query->execute([$printer_mode, $printer_usb_name, $printer_ip, $printer_port, 1]);

        if ($result === true) return "ok";
            else return "error";
    }

    public function update_printer_ticket($printer_cols, $ticket_title, $ticket_subtitle, $ticket_footer) {
        $query = $this->pdo->prepare("UPDATE printer_settings SET printer_cols = ?, ticket_title = ?, ticket_subtitle = ?, ticket_footer = ? WHERE id = ?");
        $result = $query->execute([$printer_cols, $ticket_title, $ticket_subtitle, $ticket_footer, 1]);

        if ($result === true) return "ok";
            else return "error";
    }
}

?>