<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include_once (DB_PATH."connection.php");

class User {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_users() {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE status = 1");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function insert_user($display_name, $user_name, $password, $is_admin) {
        $query = $this->pdo->prepare("INSERT INTO users (display_name, user_name, password, is_admin) VALUES (?, ?, ?, ?)");
        $result = $query->execute([$display_name, $user_name, $password, $is_admin]);

        if ($result) return "ok";
            else return "error";
    }

    public function edit_user($id, $display_name, $user_name, $password, $is_admin) {
        $query = $this->pdo->prepare("UPDATE users  SET display_name = ?, user_name = ?, password = ?, is_admin = ? WHERE id = ?");
        $result = $query->execute([$display_name, $user_name, $password, $is_admin, $id]);

        if ($result) return "ok";
            else return "error";
    }

    public function delete_user($id) {
        $query = $this->pdo->prepare("UPDATE users  SET status = ? WHERE id = ?");
        $result = $query->execute([0, $id]);

        if ($result) return "ok";
            else return "error";
    }

    public function get_user_data($id) {
        $query = $this->pdo->prepare("SELECT * from users WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function login($user_name, $password) {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE user_name = ? AND password = ?");
            $query->execute([$user_name, $password]);
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result === false) {
                return "invalid_credentials";
            } elseif ($query->rowCount() == 1) {
                $is_admin = $result->is_admin;
                $is_admin = (int) $is_admin;

                if ($is_admin === 0) return "is_not_admin";
                else {
                    session_start();
                    $_SESSION['user_id'] = $result->id;
                    $_SESSION['user_name'] = $result->user_name;
                    $_SESSION['display_name'] = $result->display_name;
                    return "logged";
                }
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