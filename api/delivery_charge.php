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

if(isset($_POST) && isset($_POST['dc'])){
    $dc = $_POST['dc'];
    $sql="UPDATE `admin` SET `deliverycharge`='$dc' LIMIT 1";
    if($conn->query($sql)){
        $result['success']=true;
    }
}

if(isset($_POST) && isset($_POST['rcode'])){
    $dc = $_POST['rcode'];
    $sql="UPDATE `admin` SET `refer_coupon_id`='$dc' LIMIT 1";
    if($conn->query($sql)){
        $result['success']=true;
    }
}

$sql = "SELECT * FROM `admin` LIMIT 1";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$result['success']=true;
$result['charge']=$row['deliverycharge'];
$rcid=$row['refer_coupon_id'];
$sql = "SELECT * FROM `coupon` WHERE `id`='$rcid' LIMIT 1;";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$result['rcid']=$row['code'];

echo json_encode($result);