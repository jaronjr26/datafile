<?php
require_once('storeclass.php');

$id = $_GET['id'];
$product = $store->get_single_product($id);
$userdetails = $store->get_userdata();
$store->add_stocks($_POST);

    if(isset($userdetails)){

        if($userdetails['access'] != "administrator"){
            
            header("Location: login.php");
        }

    }else{
        header("Location: login.php");

    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <label>Brand Name</label>
        <input type="text" name="brand_name" id="brand_name" require>
        <label>Qty</label>
        <input type="number" name="qty" id="qty" min="1" value="1">
        <label>Price</label>
        <input type="text" name="price" id="price">
        <label>Batch Number</label>
        <input type="text" name="batch_number" id="batch_number">
        <input type="hidden" name="product_id" value="<?= $product['ID']; ?>">
        <input type="hidden" name="added_by" value="<?= $userdetails['fullname'];?>" >

        <button type="submit" name="add_stock">Add Stock</button>

    </form>
</body>
</html>