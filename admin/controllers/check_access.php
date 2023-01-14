<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');

session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: /bread_factory/admin/login.php');
    exit();
} else {
    include (MODELS_PATH."User.php");
    $User = new User();

    $user_id = $_SESSION['user_id'];
    //check if is admin
    $is_admin = $User->is_admin($user_id);
    $is_admin = (int) $is_admin;

    if ($is_admin === 0) {
        header('Location: /bread_factory/admin/login.php');
        exit();
    }
}

?>