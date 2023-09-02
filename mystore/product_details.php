<?php
require_once('storeclass.php');
$id = $_GET['id'];
$product = $store->get_single_product($id);
$stocks = $store->view_all_stocks($id);
$userdetails = $store->get_userdata();
$inventory_array = array();

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
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <h1><?= $product['product_name'];?></h1>
    <h2>Category: <?= $product['product_type'];?></h2>
    <h3>Minimum Stock: <?= $product['min_stock'];?></h3>
    <br>

    <hr>
    <h2>Available Product Items</h2>

    <table border="1">

        <tr>
            <th>Action</th>
            <th>Base Stock qty</th>
            <th>SRP</th>
            <th>Sales Qty</th>
            <th>Total Sales</th>
            <th>Qty Remaining</th>
            <th>Status</th>
        </tr>

        <tbody>
        
        <?php if(is_array($stocks)){?>
        <?php foreach($stocks as $stock){?>
            <?php $sum = $stock['qty'] - $stock['sale_qty'];

            $inventory_array[]= $sum;
            ?>
            <tr class="<?= ($sum == 0) ? 'disabledbtn': ''; ?>">
            <td>
                <div id="parent_<?= $stock['ID'];?>">
                    <label><?=  $stock['vendor_name'];?> <?= $stock['qty'] ?></label>
                    <input type="number" name="qty[]" min="1" max="<?= $sum;?>" value="1">
                    <input type="hidden" name="price[]" value="<?= $stock['price'] ?>">
                    <input type="hidden" name="stock_id[]" value="<?= $stock['ID'];?>"> 
                    <button type="button" class="add_cart">Add Cart</button>
                    <button type="button" class="remove_cart" disabled id="<?= $stock['ID'];?>">Remove</button>
                </div>
            </td>
            <td><?= $stock['qty'];?></td>
            <td><?= sprintf('%01.2f', $stock['price']);?></td>
            <td><?= $stock['sale_qty']?></td>
            <td><?= sprintf('%01.2f', $stock['TotalSales']);?></td>
            <td><?= $sum;?></td>
            <td>
                <?= ($sum == 0) ? 'Out of stock': 'Available'; ?>

            </td>
            </tr>

        <?php } ?> 
        <?php } ?>


        </tbody>




    </table>


    <h4>Total Inventory :<?= $product['total'];?></h4>
    <h4>Actual Inventory :<?= array_sum($inventory_array);?></h4>

    <h4>Status: <?php
        if(array_sum($inventory_array) <= $product['min_stock'] && array_sum($inventory_array) != 0){
            echo "Low Inventory";
        }elseif(array_sum($inventory_array) == 0){
            echo "Out of stocks";
        }else{
            echo "On Sale";
        }
    
    ?></h4>


    

    <a href="products.php">Products</a>
    <a href="addnewstocks.php?id=<?= $product['ID']?>">Add new stocks</a>

    <hr>
    <h2>Cart</h2>

    <form action="checkout.php" method="post" id="check_out_form">

        <input type="hidden" name="customer_name" value="<?= $userdetails['fullname'];?>">

        <input type="hidden" name="product_id" value="<?= $product['ID'];?>">
        <button type="submit" id="checkoutbtn">Proceed to check out</button>

    </form>

    <script src="js/index.js"></script>

</body>
</html>