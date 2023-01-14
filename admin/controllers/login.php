<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["user_name"]) || !isset($_POST["password"])) {
    echo json_encode(array('status' => 'isset'));
    exit();
}

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."User.php");


$user_name = $_POST["user_name"];
$password = $_POST["password"];

$user = new User();

$result = $user->login($user_name, $password);

echo json_encode(array('status' => $result));

?>