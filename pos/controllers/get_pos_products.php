<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");

$POS = new POS();
$products = $POS->get_pos_products();
$promos = $POS->get_pos_promos();
$products_and_promos = array("products" => $products, "promos" => $promos);
echo json_encode($products_and_promos);

?>