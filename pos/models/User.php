<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class User {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function login($user_name, $password) {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE user_name = ? AND password = ?");
            $query->execute([$user_name, $password]);
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result === false) {
                return "invalid_credentials";
            } elseif ($query->rowCount() == 1) {
                session_start();
                $_SESSION['user_id'] = $result->id;
                $_SESSION['user_name'] = $result->user_name;
                $_SESSION['display_name'] = $result->display_name;
                return "logged";
            }
    }

    public function is_admin($id) {
        $query = $this->pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        $is_admin = $result === false ? 0 : $result->is_admin;
        return $is_admin;
    }
}

?>