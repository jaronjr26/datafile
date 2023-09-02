<?php
    require_once('storeclass.php');
    $store->add_user();
    $userdetails = $store->get_userdata();

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
    <h1>Add New User</h1>

    <div class="container">
        <div class="form-container">

            <form action="" method="post">
                <div class="form-input">
                    <label>Email</label>
                    <input type="text" name="email" id="email" />
                </div>

                <div class="form-input">
                    <label>Password</label>
                    <input type="password" name="password" id="password" />
                </div>

                <div class="form-input">
                    <label>First Name</label>
                    <input type="text" name="fname" id="fname" />
                </div>

                <div class="form-input">
                    <label>Last Name</label>
                    <input type="text" name="lname" id="lname" />
                </div>

                <button type="submit" name="add">Add User</button>

                

            </form>
        </div>
    </div>

</body>
</html>