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

if(isset($_POST) && isset($_POST['code']) && isset($_POST['phones'])){
    $cid = $_POST['code'];
    $phs = explode(",",$_POST['phones']);
    
    $ql1= "SELECT * FROM coupon WHERE id = '$cid' LIMIT 1;";
    $res1 = $conn->query($ql1);
    $rof = $res1->fetch_assoc();
    $code=$rof['code'];
    $des=$rof['description'];
    
    foreach($phs as $ph){
        $sql = "INSERT INTO `coupon_assign` (`id`, `coupon_id`, `user_id`) VALUES (NULL, '$cid', '$ph');";
        $conn->query($sql);
        $result['success']=true;
        
        $ql1= "SELECT * FROM user WHERE phone = '$ph' LIMIT 1;";
        $res1 = $conn->query($ql1);
        $rof = $res1->fetch_assoc();
        
        $to=$rof['fcm'];
        
        $notif = array(
                'title'=>''. $code,
                'body'=>''. $des,
                'type'=>'2',
                );
                
                
        notify($to,$notif);
    }
    
    goto print1;
}

if(isset($_POST) && isset($_POST['delete'])){
    $id = $_POST['delete'];
    
    $sql = "DELETE FROM `coupon` WHERE `id`='$id' LIMIT 1;";
    $conn->query($sql);
    $sql = "DELETE FROM `coupon_assign` WHERE `coupon_id`='$id'";
    $conn->query($sql);
    $result['success']=true;
    
    goto print1;
}

if(isset($_POST) && isset($_POST['coupon']) && isset($_POST['description'])&& isset($_POST['amount'])){
    $coupon = $_POST['coupon'];
    $desc = $_POST['description'];
    $amt = $_POST['amount'];
    
    $sql = "INSERT INTO `coupon` (`code`,`description`,`discount`) VALUES ('$coupon','$desc','$amt');";
    if($conn->query($sql)){
        $result['success']=true;
    }
    goto print1;
}
if(isset($_GET) && isset($_GET['phone'])){
    $phone = $_GET['phone'];
    $sql = "SELECT * FROM `coupon_assign` WHERE `user_id` = '$phone'";
    $res = $conn->query($sql);
    $result['data']=array();
    if($res->num_rows>0)
    {
        $index=array();
        while($row=$res->fetch_assoc()){
            $index['id']=$row['coupon_id'];
            $cid = $row['coupon_id'];
            
            $sq = "SELECT * FROM `coupon` WHERE `id` = '$cid'";
            $re = $conn->query($sq);
            $ro = $re->fetch_assoc();
            $index['code']=$ro['code'];
            $index['discount']=$ro['discount'];
            $index['description']=$ro['description'];
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No Counpons Found";
    }
    goto print1;
}
if(isset($_GET) && !isset($_GET['phone'])){
    
    $sql = "SELECT * FROM `coupon` ORDER BY `id` DESC";
    $res = $conn->query($sql);
    $result['data']=array();
    if($res->num_rows>0)
    {
        $index=array();
        while($row=$res->fetch_assoc()){
            $index['id']=$row['id'];
            $index['code']=$row['code'];
            $index['discount']=$row['discount'];
            $index['description']=$row['description'];
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
    else{
        $result['message']="No Counpons Found";
    }
    goto print1;
}
print1: echo json_encode($result);