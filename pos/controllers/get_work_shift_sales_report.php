<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (MODELS_PATH."POS.php");
include (MODELS_PATH."WorkShift.php");

$POS = new POS();
$WorkShift = new WorkShift();

//get work shift opening date 
$current_workshift = $WorkShift->check_work_shift();
$datetime = $current_workshift->datetime;

$sales = $POS->get_sales($datetime);

$report = array();
foreach($sales as $sale) {
    //get products/promos by each sale :)
    $sale_id = $sale->id;
    
    $sale_products = $POS->get_sale_products($sale_id);
    $sale_promos = $POS->get_sale_promos($sale_id);

    $items = array("products"=>$sale_products, "promos"=>$sale_promos);
    $report[] = $items;
}

echo json_encode($report);

?>