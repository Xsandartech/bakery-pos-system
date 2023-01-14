<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST["id"])) {
    echo "isset";
    exit();
}

$id = $_POST["id"];

include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."User.php");

$User = new User();

$result = $User->delete_user($id);

echo json_encode(array('status' => $result));

?>