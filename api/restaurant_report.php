<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Kolkata');

$result=array();
$result['success']=false;
if(isset($_GET) && isset($_GET['res_id'])){
    $rid=$_GET['res_id'];
    $sql = "SELECT COUNT(*),SUM(total)-SUM(delivery) FROM orders WHERE restaurant='$rid';";
    if($res=$conn->query($sql)){
        $row=$res->fetch_assoc();
        $result['total']=$row['SUM(total)-SUM(delivery)'];
        $result['orders_total']=$row['COUNT(*)'];
        $result['success']=true;
    }
    $date=date('d/m/Y').'%';
    $sql = "SELECT COUNT(*),SUM(total)-SUM(delivery) FROM `orders` WHERE `created` LIKE '$date' AND restaurant='$rid';";
    if($res=$conn->query($sql)){
        $row=$res->fetch_assoc();
        $result['total_today']=$row['SUM(total)-SUM(delivery)'];
        $result['orders_today']=$row['COUNT(*)'];
        $result['success']=true;
    }
}
echo json_encode($result);