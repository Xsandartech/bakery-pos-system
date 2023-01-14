<?php

$_POST = json_decode(file_get_contents('php://input'), true);

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."User.php");


if(!isset($_POST["id"])) {
    echo json_encode(array('status' => "isset"));
    exit();
}
$id = $_POST["id"];

$User = new User();
$user_data = $User->get_user_data($id);
echo json_encode($user_data);

?>