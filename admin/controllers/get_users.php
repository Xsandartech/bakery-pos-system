<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (MODELS_PATH."User.php");

$User = new User();
$users = $User->get_users();

echo json_encode($users);
?>