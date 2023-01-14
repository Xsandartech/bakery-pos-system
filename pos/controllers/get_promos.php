<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Promo.php");

$Promo = new Promo();
$promos = $Promo->get_promos();
echo json_encode($promos);

?>