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
    $items=['user','restaurant','product','orders','coupon','category','rider'];
    foreach($items as $item){
        $sql = "SELECT COUNT(*) FROM $item";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $result[$item]=$row['COUNT(*)'];
    }
    $sql = "SELECT SUM(total) FROM orders";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $result['earning']=$row['SUM(total)'];
    
    $result['success']=true;
}

echo json_encode($result);