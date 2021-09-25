<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
require __DIR__ . '/notification.php';
date_default_timezone_set('Asia/Kolkata');
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result=array();
$result['success']=false;


if(isset($_GET) && isset($_GET['rider_id']) && isset($_GET['status'])){
    $rid = $_GET['rider_id'];
    $status=$_GET['status'];
    if($status=='0')
    {
        $sql="SELECT * FROM `orders` WHERE `rider` = '$rid' AND status!='COMPLETED' ORDER BY `id` DESC";
    }
    else
    {
        $sql="SELECT * FROM `orders` WHERE `rider` = '$rid' AND status='COMPLETED' ORDER BY `id` DESC";
    }
    $res = $conn->query($sql);
    $result['data']=array();
    if($res->num_rows>0){
        $index=array();
        while($row=$res->fetch_assoc())
        {
            $index['id']=$row['id'];
            $index['uid']=$row['uid'];
            $index['name']=$row['name'];
            $index['phone']=$row['phone'];
            $index['address']=$row['address'];
            $index['product']=$row['product'];
            $index['delivery']=$row['delivery'];
            $index['total']=$row['total'];
            $index['restaurant']=$row['restaurant'];
            $index['status']=$row['status'];
            $index['created']=$row['created'];
            $index['payment_type']=$row['payment_type'];
            $index['payment_status']=$row['payment_status'];
            $index['transaction_id']=$row['transaction_id'];
            
            $uid = $row['uid'];
            $ql = "SELECT * FROM `user` WHERE `phone` = '$uid' LIMIT 1;";
            $re = $conn->query($ql);
            $ro = $re->fetch_assoc();
            $index['user_image']=$ro['image'];
            
            $rr= $row['restaurant'];
            $sq = "SELECT * FROM `restaurant` WHERE `id` = '$rr' LIMIT 1";
            $r3 = $conn->query($sq);
            $ro4 = $r3->fetch_assoc();
            $index['res_name']=$ro4['name'];
            $index['res_address']=$ro4['address'];
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No orders yet!";
    }
    goto print1;
}

print1: echo json_encode($result);