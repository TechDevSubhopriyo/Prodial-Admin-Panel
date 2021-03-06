<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
require __DIR__ . '/notification.php';
use Kreait\Firebase\Factory;
date_default_timezone_set('Asia/Kolkata');
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Kolkata');
$result=array();
$result['success']=false;

function distance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);
  
  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
  $angle = atan2(sqrt($a), $b);

  return $angle * $earthRadius;
}

if(isset($_GET) && isset($_GET['phone'])){
    $ph = $_GET['phone'];
    $sql="SELECT * FROM `orders` WHERE `uid` = '$ph' ORDER BY `id` DESC";
    $res = $conn->query($sql);
    $result['data']=array();
    if($res->num_rows>0){
        $index=array();
        while($row=$res->fetch_assoc())
        {
            $index['id']=$row['id'];
            $index['status']=$row['status'];
            $index['date']=$row['created'];
            $index['total']=$row['total'];
            $index['payment_type']=$row['payment_type'];
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No orders yet!";
    }
    goto print1;
}

if(isset($_GET) && isset($_GET['restaurant_id'])){
    $resid = $_GET['restaurant_id'];
    $sql="SELECT * FROM `orders` WHERE `restaurant` = '$resid' AND `status` != 'COMPLETED' ORDER BY `id` DESC";
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
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No orders yet!";
    }
    goto print1;
}
if(isset($_GET) && isset($_GET['restaurant_past'])){
    $date0=date('d/m/Y', strtotime('-0 days')).'%';
    $date1=date('d/m/Y', strtotime('-1 days')).'%';
    $date2=date('d/m/Y', strtotime('-2 days')).'%';
    $resid = $_GET['restaurant_past'];
    $sql="SELECT * FROM `orders` WHERE `restaurant` = '$resid' AND `status` = 'COMPLETED' AND (`created` LIKE '$date0' OR `created` LIKE '$date1' OR `created` LIKE '$date2') ORDER BY `id` DESC";
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
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No orders yet!";
    }
    goto print1;
}
if(isset($_GET) && isset($_GET['order'])){
    $sql="SELECT * FROM orders ORDER BY id DESC";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $index=array();
        $result['data']=array();
        while($row=$res->fetch_assoc()){
            $index['uid']=$row['uid'];
            $index['name']=$row['name'];
            $index['phone']=$row['phone'];
            $index['address']=$row['address'];
            $index['ptype']=$row['payment_type'];
            $index['delivery']=$row['delivery'];
            $index['total']=$row['total'];
            $index['restaurant']=$row['restaurant'];
            $index['status']=$row['status'];
            $index['id']=$row['id'];
            $index['tid']=$row['transaction_id'];
            $index['created']=$row['created'];
            $index['rider']=$row['rider'];
            
            $prod =explode("$",$row['product']);
            $index['product']=array();
            $p=array();
            foreach($prod as $product){
                $pidt = explode(",",$product);
                $p['product_id']=$pidt[0];
                $p['item']=$pidt[1];
                $p['iprice']=$pidt[2];
                $pi = $pidt[0];
                $s2 = "SELECT * FROM `product` WHERE `id` = '$pi' LIMIT 1";
                $r2 = $conn->query($s2);
                $ro3 = $r2->fetch_assoc();
                $p['pr_name']=$ro3['name'];
                $p['pr_image']=$ro3['image'];
                $p['pr_desc']=$ro3['description'];
                
                array_push($index['product'],$p);
            }
            $rr= $row['restaurant'];
            $sq = "SELECT * FROM `restaurant` WHERE `id` = '$rr' LIMIT 1";
            $r3 = $conn->query($sq);
            $ro4 = $r3->fetch_assoc();
            $index['res_name']=$ro4['name'];
            
            array_push($result['data'],$index);
            $result['success']=true;
        }
    }
    goto print1;
}

if(isset($_GET) && isset($_GET['order_id'])){
    $oid = $_GET['order_id'];
    $sql="SELECT * FROM orders WHERE `id` = '$oid' LIMIT 1";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $index=array();
        $result['data']=array();
        while($row=$res->fetch_assoc()){
            $index['uid']=$row['uid'];
            $index['name']=$row['name'];
            $index['phone']=$row['phone'];
            $index['address']=$row['address'];
            $index['ptype']=$row['payment_type'];
            $index['delivery']=$row['delivery'];
            $index['total']=$row['total'];
            $index['restaurant']=$row['restaurant'];
            $index['status']=$row['status'];
            $index['id']=$row['id'];
            $index['tip']=$row['tip'];
			$index['pay_restaurant']=$row['total']-$row['delivery']-$row['tip'];
            $index['tid']=$row['transaction_id'];
            $index['geo_location']=$row['geo_location'];
            $index['created']=$row['created'];
            $index['payment_status']=$row['payment_status'];
            $index['remaining_time']=$row['time_remaining'];
            
            $prod =explode("$",$row['product']);
            $index['product']=array();
            $p=array();
            foreach($prod as $product){
                $pidt = explode(",",$product);
                $p['product_id']=$pidt[0];
                $p['item']=$pidt[1];
                $p['iprice']=$pidt[2];
                $pi = $pidt[0];
                $s2 = "SELECT * FROM `product` WHERE `id` = '$pi' LIMIT 1";
                $r2 = $conn->query($s2);
				$p['pr_name']="Product was removed";
				$p['pr_image']="";
				$p['pr_desc']="";
                while($ro3 = $r2->fetch_assoc()){
					$p['pr_name']=$ro3['name'];
					$p['pr_image']=$ro3['image'];
					$p['pr_desc']=$ro3['description'];
				}
                
                array_push($index['product'],$p);
            }
            $rr= $row['restaurant'];
            $sq = "SELECT * FROM `restaurant` WHERE `id` = '$rr' LIMIT 1";
            $r3 = $conn->query($sq);
            $ro4 = $r3->fetch_assoc();
            $index['res_name']=$ro4['name'];
            $index['res_image']=$ro4['image'];
            $index['res_address']=$ro4['address'];
            $index['res_phone']=$ro4['phone'];
            $index['res_location']=$ro4['location'];
            
            $person_uid=$row['uid'];
            $sq4="SELECT * FROM user WHERE phone='$person_uid' LIMIT 1";
            $re4=$conn->query($sq4);
            $user=$re4->fetch_assoc();
            $index['user_image']=$user['image'];
            
            $rid=$row['rider'];
            $index['rider_id']=$rid;
			$index['rider_name']="";
			$index['rider_image']="";
			$index['rider_review']="";
			$index['rider_phone']="";
            if($rid!=0){
                $sq4="SELECT * FROM rider WHERE id='$rid' LIMIT 1";
                $re4=$conn->query($sq4);
                $rider=$re4->fetch_assoc();
                $index['rider_name']=$rider['name'];
                $index['rider_image']=$rider['image'];
                $index['rider_review']=$rider['review'];
                $index['rider_phone']=$rider['phone'];
            }
            
            array_push($result['data'],$index);
            $result['success']=true;
        }
    }
    goto print1;
}
if(isset($_POST) && isset($_POST['order']) && isset($_POST['rider'])){
    $oid = $_POST['order'];
    $rid = $_POST['rider'];
    $sql = "UPDATE orders SET rider = '$rid' WHERE id = '$oid' LIMIT 1;";
    if($conn->query($sql))
    {
        $sql2 = "UPDATE orders SET status='ASSIGNED' WHERE id = '$oid' AND status='PENDING' LIMIT 1;";
        $conn->query($sql2);
        $result['success']=true;
        $sq="SELECT u.fcm,r.name FROM `orders` AS o LEFT JOIN user AS u ON u.phone=o.uid LEFT JOIN rider AS r ON r.id=o.rider WHERE o.id='$oid' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $r=$ro['name'];
        $notif = array(
            'title'=>'Huraaay! Order ID-'.$oid,
            'body'=>'Your order is assigned to '.$r,
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$oid
            );
        notify($to,$notif);
        $sq="SELECT r.fcm,r.name FROM `orders` AS o LEFT JOIN user AS u ON u.phone=o.uid LEFT JOIN rider AS r ON r.id=o.rider WHERE o.id='$oid' LIMIT 1;";
        $re=$conn->query($sq);
        $ro=$re->fetch_assoc();
        $to=$ro['fcm'];
        $notif = array(
            'title'=>'New Order Assigned! Order ID-'.$oid,
            'body'=>'Please pick up the order and deliver',
            'type'=>1,    // 1-> order, 2-> coupon, 3-> anything
            'id'=>$oid
            );
        notify($to,$notif);
    }
    goto print1;
}
$data = json_decode(file_get_contents("php://input"));
$uid=$data->uid;
$name=$data->name;
$phone=$data->phone;
$address=$data->address;
$payment_type=$data->payment_type;
$delivery=$data->delivery;
$total=$data->total;
$restaurant=$data->restaurant;
$product=$data->product;
$dat=$data->dat;
$promo=$data->promo;
$tid=$data->tid;
$tip=$data->tip;
$geo=$data->geo_location;
if(!isset($uid))
{
    goto print1;
}
$sql = "INSERT INTO `orders` (`id`, `uid`, `name`, `phone`, `address`, `product`, `delivery`, `total`, `restaurant`, `status`, `payment_type`, `payment_status`, `transaction_id`, `created`,`tip`,`geo_location`) VALUES (NULL, '$uid', '$name', '$phone', '$address', '$product', '$delivery', '$total', '$restaurant', 'PENDING', '$payment_type', 'PENDING', '$tid', '$dat','$tip','$geo');";

if($conn->query($sql)){
    
    $result['success']=true;
    $result['message']="Order Placed";
    
    $result['order_id']= $conn->insert_id;
    $oid=$conn->insert_id;
    
    $prod =explode("$",$product);
    $p=array();
    foreach($prod as $product){
        $pidt = explode(",",$product);
        $pid=$pidt[0];
        $pit=$pidt[1];
        
        $s2 = "UPDATE `product` SET `stock` = `stock`-'$pit' WHERE `id` = '$pid' LIMIT 1";
        //$conn->query($s2);
    }
    
    if(strlen($promo)>0){
        $ql = "DELETE FROM `coupon_assign` WHERE `coupon_id` = '$promo' AND `user_id` = '$phone' LIMIT 1";
        $conn->query($ql);
    }
    
    $ql1= "SELECT * FROM `user` WHERE `phone` = '$uid' LIMIT 1;";
    $res1 = $conn->query($ql1);
    $rof = $res1->fetch_assoc();
    
    $to=$rof['fcm'];
    
    $notif = array(
            'title'=>'Hey '.$name,
            'body'=>'Your order has been placed! Thank you for choosing Prodial. Order id #'.$oid,
            'type'=>'1',    // 1-> order, 2-> coupon, 3-> anything
            'id'=>''.$oid,
            );
            
    notify($to,$notif);
    
    $ql1= "SELECT * FROM `restaurant` WHERE `id` = '$restaurant' LIMIT 1;";
    $res1 = $conn->query($ql1);
    $rof = $res1->fetch_assoc();
    
    $to=$rof['fcm'];
    
    $notif = array(
            'title'=>'New Order Arrived!',
            'body'=>'You have got a new order from '.$name,
            'type'=>'1',    // 1-> order, 2-> coupon, 3-> anything
            'id'=>''.$oid,
            );
            
    notify($to,$notif);
    
    $ql1= "SELECT * FROM `rider` WHERE `status` = '1';";
    $res1 = $conn->query($ql1);
    while($rof = $res1->fetch_assoc())
    {
        $to=$rof['fcm'];
        $g = explode(",",$rof['geo_location']);
        $gf = explode(",",$geo);
        $d=0;
        $d= distance($g[0],$g[1],$gf[0],$gf[1]);
        
        $notif = array(
                'title'=>'New Order Available',
                'body'=>'Open the app and accept now before anyone else!',
                'type'=>'0',    // 1-> order, 2-> coupon, 3-> anything
                'id'=>''.$oid,
                );
        if($d<10){        
            notify($to,$notif);
        }
    }
    
    
}
else{
    $result['message']="Some Error Occurred";
}

print1: echo json_encode($result);
?>