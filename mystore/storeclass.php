<?php
 
class MyStore{
    
    private $server = "mysql:host=localhost;dbname=mystore";
    private $user = "root";
    private $pass = "9804261";
    private $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    protected $con;

    public function openConnection(){
        try{

            $this->con = new PDO($this->server, $this->user, $this->pass,  $this->options);
            return $this->con;

        }catch(PDOException $e){
            echo "Connection problem". $e->getMessage();
        }
    }

    public function closeConnection(){
        $this->con = null;
    }

    public function getUsers(){
        $connection = $this->openConnection();
        $stmt = $connection->prepare("SELECT * FROM members");
        $stmt->execute();
        $users = $stmt->fetchAll();
        $userCount = $stmt->rowCount();

        if($userCount > 0){
            return $users;
        }else{
            return 0;
        }
    }

    public function login(){

        if(isset($_POST['submit'])){

            $password = md5($_POST['password']);
            $username = $_POST['email'];

            $connection = $this->openConnection();
            $stmt = $connection->prepare("SELECT * FROM members WHERE email = ? AND password = ?");
            $stmt->execute([$username,$password]);
            $user = $stmt->fetch();
            $total = $stmt->rowCount();

            if($total > 0){
                echo "Welcome". " ".$user['last_name'];
                $this->set_userdata($user);
                header("Location: products.php");
            }else{
                echo "Login Failed";
            }


        }
    }

    public function set_userdata($array){

        if(!isset($_SESSION)){
            session_start();
        }

        $_SESSION['userdata'] = array(

            "fullname" =>$array['first_name']." ".$array['last_name'],
            "access" => $array['access']

        );

        return $_SESSION['userdata'];

    }

    public function get_userdata(){

        if(!isset($_SESSION)){
            session_start();
        }

        if(isset($_SESSION['userdata'])){
            return $_SESSION['userdata'];
        }else{
            return null;
        }

        


    }

    public function logout(){

        if(!isset($_SESSION)){
            session_start();
        }

        $_SESSION['userdata'] = null;
        unset($_SESSION['userdata']);

    }

    public function check_user($email){

            $connection = $this->openConnection();
            $stmt = $connection->prepare("SELECT * FROM members WHERE email = ?");
            $stmt->execute([$email]);
            $total = $stmt->rowCount();

            return $total;

    }

    public function add_user(){

        if(isset($_POST['add'])){

            $email = $_POST['email'];
            $password = md5($_POST['password']);
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];

            if($this->check_user($email) == 0){

                $connection = $this->openConnection();
                $stmt = $connection->prepare("INSERT INTO members(`email`,`password`,`first_name`,`last_name`)VALUES(?,?,?,?)");
                $stmt->execute([$email,$password,$fname,$lname]);

            }else{

                echo "User Already Exist";

            }

            
        }

    }

    public function show_404(){

        http_response_code(404);
        echo "Page Not Found";
        die;
    }

    public function check_product_exist($name){

        $connection = $this->openConnection();
        $stmt = $connection->prepare("SELECT LOWER('product_name') FROM products WHERE product_name = ?");
        $stmt->execute([strtolower($name)]);
        $total = $stmt->rowCount();

        return $total;
    }

    public function add_product(){

        if(isset($_POST['add_product'])){

            $product_name = $_POST['product_name'];
            $product_type = $_POST['product_type'];
            $min_stock = $_POST['min_stock'];

            if($this->check_product_exist($product_name) == 0){

                $connection = $this->openConnection();
                $stmt = $connection->prepare("INSERT INTO products (`product_name`,`product_type`,`min_stock`) VALUES (?,?,?)");
                $stmt->execute([$product_name, $product_type, $min_stock]);
                header("Location: products.php");

            }else{
                echo "product already exist";
            }





        }
    }

    public function get_products(){

        $connection = $this->openConnection();
        $stmt = $connection->prepare("SELECT * FROM products");
        $stmt->execute();
        $product = $stmt->fetchall();
        $total = $stmt->rowCount();

        if($total > 0){

            return $product;
        }else{
            return FALSE;
        }

    }

    public function get_single_product($id){

        $connection = $this->openConnection();
        $stmt = $connection->prepare("SELECT t1.ID, product_name, product_type, min_stock, SUM(qty) As total FROM (SELECT * FROM products WHERE products.ID = ?) t1 INNER JOIN product_items t2 ON t1.ID = t2.product_id");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0){

            return $product;

        }else{

            return $this->show_404();
        }


    }

    public function get_total_qty($product_id){
            $connection = $this->openConnection();
            $stmt = $connection->prepare("SELECT *, SUM(qty) as total FROM product_items WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product_qty = $stmt->fetch();

            return $product_qty['total'];


    }

    public function add_stocks(){

        if(isset($_POST['add_stock'])){

            $brand_name = $_POST['brand_name'];
            $qty = $_POST['qty'];
            $batch_number = $_POST['batch_number'];
            $product_id = $_POST['product_id'];
            $added_by = $_POST['added_by'];
            $price = $_POST['price'];

            $connection = $this->openConnection();
            $stmt = $connection->prepare("INSERT INTO product_items (`product_id`, `qty`, `vendor_name`, `added_by`,`batch_number`,`price`) VALUES ( ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([$product_id, $qty, $brand_name, $added_by, $batch_number, $price]);

            header("Location: product_details.php?id=".$product_id);

        }
    }

    public function view_all_stocks($product_id){

        $connection = $this->openConnection();

        // $stmt = $connection->prepare("SELECT * FROM product_items WHERE product_id = ?");

        $stmt = $connection->prepare("SELECT t1.ID, t1.vendor_name, t1.price, t1.qty, SUM(t2.qty) as sale_qty, SUM(t2.qty * t2.price) as TotalSales FROM product_items t1 LEFT JOIN sales t2 ON t1.ID = t2.stock_id WHERE t1.product_id = ? GROUP BY t1.ID");

        $stmt->execute([$product_id]);
        $stocks = $stmt->fetchall();
        $total = $stmt->rowCount();

        if($total > 0){

            return $stocks;

        }else{

            return FALSE;
        }
    }

    public function get_stock_details($stock_id){

        $connection = $this->openConnection();
        $stmt = $connection->prepare("SELECT * FROM product_items WHERE ID = ?");
        $stmt->execute([$stock_id]);
        $stocks = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0){

            return $stocks;

        }else{

            return FALSE;
        }

    }

    public function insert_sales($stock_id, $qty, $price, $product_id, $customer_name){

        $item = $this->get_stock_details($stock_id);
        $brand = $item['vendor_name'];



        $connection = $this->openConnection();
        $stmt = $connection->prepare("INSERT INTO sales (`product_id`, `stock_id`, `brand_name`, `qty`, `price`, `customer_name`)VALUE(?,?,?,?,?,?)");
        $stmt->execute([$product_id, $stock_id, $brand, $qty, $price, $customer_name]);

    }

}

$store = new MyStore();