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
    
    $date0=date('d/m/Y', strtotime('-0 days')).'%';
    $date1=date('d/m/Y', strtotime('-1 days')).'%';
    $date2=date('d/m/Y', strtotime('-2 days')).'%';
    $date3=date('d/m/Y', strtotime('-3 days')).'%';
    $date4=date('d/m/Y', strtotime('-4 days')).'%';
    $date5=date('d/m/Y', strtotime('-5 days')).'%';
    $date6=date('d/m/Y', strtotime('-6 days')).'%';
    $sql = "SELECT COUNT(*),SUM(total-delivery-tip) as report FROM orders WHERE restaurant='$rid' AND (`created` LIKE '$date0' OR  `created` LIKE '$date1' OR  `created` LIKE '$date2' OR  `created` LIKE '$date3' OR  `created` LIKE '$date4' OR  `created` LIKE '$date5' OR  `created` LIKE '$date6');";
    if($res=$conn->query($sql)){
        $row=$res->fetch_assoc();
        $result['total']=$row['report'];
        $result['orders_total']=$row['COUNT(*)'];
        $result['success']=true;
        if($result['total']==null){
            $result['total']=0;
        }
    }
    $date=date('d/m/Y').'%';
    $sql = "SELECT COUNT(*),SUM(total-delivery-tip) as report FROM `orders` WHERE `created` LIKE '$date' AND restaurant='$rid';";
    if($res=$conn->query($sql)){
        $row=$res->fetch_assoc();
        $result['total_today']=$row['report'];
        $result['orders_today']=$row['COUNT(*)'];
        $result['success']=true;
        if($result['total_today']==null){
            $result['total_today']=0;
        }
    }
}
echo json_encode($result);