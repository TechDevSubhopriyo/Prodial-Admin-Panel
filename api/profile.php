<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result=array();
$result['success']=false;

if(isset($_GET)){
    $sql = "SELECT * FROM admin";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $result['username']=$row['username'];
    $result['password']=$row['password'];
    $result['success']=true;
}

if(isset($_POST) && isset($_POST['username'])){
    
    $u = $_POST['username'];
    $p = $_POST['password'];
    $sql = "UPDATE admin SET username = '$u', password = '$p' WHERE id = '1' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Updated";
    }
}

if(isset($_POST) && isset($_POST['res_id']) && isset($_POST['res_name']) && isset($_POST['res_phone']) && isset($_POST['res_email']) && isset($_POST['res_pass'])&& isset($_POST['res_address'])&& isset($_POST['res_fcm'])){
    
    $id=$_POST['res_id'];
    $name=$_POST['res_name'];
    $phone =$_POST['res_phone'];
    $email =$_POST['res_email'];
    $pass =$_POST['res_pass'];
    $addr =$_POST['res_address'];
    $fcm =$_POST['res_fcm'];
    $image =$_POST['res_image'];
    
    $sql = "UPDATE restaurant SET name = '$name',phone = '$phone',address = '$addr',email = '$email', password = '$pass',fcm = '$fcm'";
    
    $result['img_url']="";
    if(strlen($image)>0)
    {
        $imageStore=time().".jpeg";
        $dir= '../restaurant_image/'.$imageStore;
        file_put_contents($dir,base64_decode($image));
        $path = $c->getHost().'restaurant_image/'.$imageStore;
        $sql.=",image='$path'";
        $result['img_url']=$path;
    }
    
    $sql.=" WHERE id = '$id' LIMIT 1;";
    
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Updated";
    }
    else{
        $result['message']="Some error occurred";
    }
}

echo json_encode($result);