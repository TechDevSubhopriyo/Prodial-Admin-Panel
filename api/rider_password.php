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

if(isset($_GET) && isset($_GET['phone'])){
    $ph = $_GET['phone'];
    $sql="SELECT * FROM `rider` WHERE phone='$ph' LIMIT 1;";
    if($res=$conn->query($sql)){
        if($res->num_rows==1)
            $result['success']=true;
        else
            $result['message']="No account found";
    }
    else{
        $result['message']="Some error occurred";
    }
}

if(isset($_POST) && isset($_POST['phone']) && isset($_POST['password']))
{
    $phone=$_POST['phone'];
    $pass=$_POST['password'];
    $sql="UPDATE `rider` SET `password`='$pass' WHERE `phone`='$phone' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
    }
    else{
        $result['message']="Some error occurred";
    }
}

echo json_encode($result);