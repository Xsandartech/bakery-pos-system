<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');

session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: /bread_factory/pos/login.php');
    exit();
} else {
    include (MODELS_PATH."WorkShift.php");
    $work_shift = new WorkShift();
    $current_work_shift = $work_shift->check_work_shift();
    $user_id = $_SESSION['user_id'];

    //first time using my POS system :')
    if ($current_work_shift === "empty") header('Location: new_work_shift.php'); //open new work shift
        else {
            $is_finished = $current_work_shift->is_finished;

            //open new work shift
            if ($is_finished) header('Location: /bread_factory/pos/new_work_shift.php');
        }
}

?>