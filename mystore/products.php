<?php
    require_once('storeclass.php');
    $product = $store->get_products();


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

    <ul>
        <?php foreach($product as $product){?>
            <li><a href="product_details.php?id=<?= $product['ID'];?>"><?= $product['product_name'];?> | <?=$product['min_stock'];?></a></li>       
            <?php } ?>
    </ul>

    <a href="addnewproduct.php">Add new</a>
    <a href="logout.php">Logout</a>
    
</body>
</html>