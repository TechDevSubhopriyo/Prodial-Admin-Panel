<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
require __DIR__ . '/notification.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result=array();
$result['success']=false;

if(isset($_POST) && isset($_POST['restaurant_id'])  && isset($_POST['status'])){
    $s = $_POST['status'];
    $r = $_POST['restaurant_id'];
    $sql = "UPDATE restaurant SET status= '$s' WHERE id = '$r' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
    }
}

if(isset($_POST) && isset($_POST['rider_id'])  && isset($_POST['status']) && isset($_POST['fcm'])){
    $s = $_POST['status'];
    $r = $_POST['rider_id'];
    $f = $_POST['fcm'];
    $sql = "UPDATE rider SET status= '$s',fcm='$f' WHERE id = '$r' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
    }
}
if(isset($_POST) && isset($_POST['product_id'])  && isset($_POST['stock'])){
    $s = $_POST['stock'];
    $p = $_POST['product_id'];
    $sql = "UPDATE product SET stock= '$s' WHERE id = '$p' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
    }
}
if(isset($_POST) && isset($_POST['order_id'])  && isset($_POST['time'])){
    $t = $_POST['time'];
    $o = $_POST['order_id'];
    $sql = "UPDATE orders SET time_remaining= '$t' WHERE id = '$o' LIMIT 1;";
    if($t<=5 )
    {
        //$t="READY";
        $sql = "UPDATE orders SET time_remaining= '$t',status='READY' WHERE id = '$o' LIMIT 1;";
        $sq="SELECT u.fcm FROM `orders` AS o LEFT JOIN user AS u ON u.phone=o.uid WHERE o.id='$o' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'Huraaay!',
            'body'=>'Your order is Ready and Waiting to be picked up',
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$o
            );
        notify($to,$notif);
        
        $sq="SELECT r.fcm FROM `orders` AS o LEFT JOIN rider AS r ON r.id=o.rider WHERE o.id='$o' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'Order Updates!',
            'message'=>'Order is Ready and Waiting to be picked up',
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$o
            );
        notify($to,$notif);
    }
    else{
        $sq="SELECT u.fcm FROM `orders` AS o LEFT JOIN user AS u ON u.phone=o.uid WHERE o.id='$o' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'Huraaay! Order ID-'.$o,
            'body'=>'Your order will be Ready in '.$t.' Minutes',
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$o
            );
        notify($to,$notif);
        
        $sq="SELECT r.fcm FROM `orders` AS o LEFT JOIN rider AS r ON r.id=o.rider WHERE o.id='$o' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'Order Updates!',
            'message'=>'Order will be Ready in '.$t.' Minutes',
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$o
            );
        notify($to,$notif);
    }
    if($conn->query($sql)){
        $result['success']=true;
    }
}
if(isset($_POST) && isset($_POST['order_id'])  && isset($_POST['status'])){
    $s = $_POST['status'];
    $p = $_POST['order_id'];
    $sql = "UPDATE `orders` SET `status`= '$s' WHERE `id` = '$p' LIMIT 1;";
    if($s=="COMPLETED"){
        $sql = "UPDATE `orders` SET `status`= '$s',`payment_status`='PAID' WHERE `id` = '$p' LIMIT 1;";
    }
    if($conn->query($sql)){
        $result['success']=true;
        $sq="SELECT u.fcm FROM `orders` AS o LEFT JOIN user AS u ON u.phone=o.uid WHERE o.id='$p' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'Huraaay! Order ID-'.$p,
            'body'=>'Your order is '.$s,
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$p
            );
        notify($to,$notif);
    }
    else{
        $result['message']="Some error occured!";
    }
}
if(isset($_POST) && isset($_POST['forgot'])){
    $s = $_POST['forgot'];
    $sql = "UPDATE `restaurant` SET `password`= '$s' WHERE `phone` = '$s' LIMIT 1;";
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Your Temporary Password is ".$s;
    }
    else{
        $result['message']="Some error occured!";
    }
}
echo json_encode($result);