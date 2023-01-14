<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."Product.php");

$Product = new Product();
$products = $Product->get_products();
echo json_encode($products);

?>