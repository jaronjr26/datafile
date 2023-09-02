<?php

include_once('storeclass.php');
$total = count($_POST['stock_id']);

for ($i = 0; $i < $total; $i++){

    $item = $store->insert_sales($_POST['stock_id'][$i], $_POST['qty'][$i], $_POST['price'][$i],$_POST['product_id'],$_POST['customer_name'][$i]);

}

header("Location: ".$_SERVER['HTTP_REFERER']);