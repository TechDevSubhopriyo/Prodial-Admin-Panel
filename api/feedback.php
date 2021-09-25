<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
require __DIR__ . '/notification.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result = array();
$result['success'] = false;

$data = json_decode(file_get_contents("php://input"));


if(isset($_POST)){
    $uid=$data->uid;
    $msg=$data->feedback;
    
    $sql = "INSERT INTO `feedback` (`uid`, `message`) VALUES ('$uid', '$msg');";
    $conn->query($sql);
    $result['success']=true;
    $result['message']="Thank you for your valuable feedback";
    
    $ql1= "SELECT * FROM user WHERE phone = '$uid' LIMIT 1;";
    $res1 = $conn->query($ql1);
    $rof = $res1->fetch_assoc();
    
    $to=$rof['fcm'];
    
    $notif = array(
            'title'=>'Thanks mate!',
            'body'=>'We appreciate you valuable feedback',
            'type'=>'3',
            );
            
            
    notify($to,$notif);
    
    goto print1;
}

if(isset($_GET)){
    $sql = "SELECT * FROM `feedback`;";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $result['data']=array();
        $index=array();
        while($row=$res->fetch_assoc()){
            $index['uid']=$row['uid'];
            $index['feedback']=$row['message'];
            $ui= $index['uid'];
            $sq = "SELECT * FROM user WHERE `phone` = '$ui' LIMIT 1";
            $re = $conn->query($sq);
            $ro = $re->fetch_assoc();
            $index['name']=$ro['username'];
            
            array_push($result['data'],$index);
        }
        $result['success']=true;
    }
}
print1: echo json_encode($result);