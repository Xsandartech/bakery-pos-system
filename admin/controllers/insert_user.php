<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["user_name"]) || !isset($_POST["display_name"]) || !isset($_POST["password"]) || !isset($_POST["is_admin"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}

$user_name = $_POST["user_name"];
$display_name = $_POST["display_name"];
$password = $_POST["password"];
$is_admin = $_POST["is_admin"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."User.php");

$User = new User();
$result = $User->insert_user($display_name, $user_name, $password, $is_admin);

echo json_encode(array('status' => $result));

?>