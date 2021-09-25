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
    $data = json_decode(file_get_contents("php://input"));
    $pid = $data->product_id;
    $ph = $data->phone;
    $sql = "SELECT * FROM `product_rating` WHERE `product_id` ='$pid' AND `phone`='$ph' LIMIT 1";
    $res = $conn->query($sql);
    if($res->num_rows==1){
        $row = $res->fetch_assoc();
        $result['success']=true;
        $result['rating']= $row['rating'];
        $result['description']= $row['description'];
    }
}
if(isset($_POST)){
    
    $data = json_decode(file_get_contents("php://input"));
    $type=$data->type;
    $id=$data->id;
    $feedback=$data->feedback;
    $phone=$data->phone;
    $rating=$data->rating;
    
    if($type=='0'){  //For product Rating
        
        $sql = "INSERT INTO `product_rating` (`phone`,`product_id`,`description`,`rating`,`type`) VALUE ('$phone','$id','$feedback','$rating','0');";
        $conn->query($sql);
        $sql = "UPDATE `product` SET `rating`=(`rating`*`total_rating`+'$rating')/(`total_rating`+1),`total_rating`=`total_rating`+1 WHERE `id` = '$id'";
        $conn->query($sql);
        $result['success']=true;
        $result['message']='Thank you for your feedback';
    }
    else if($type=='1'){  //For restaurant Rating
        
        $sql = "INSERT INTO `product_rating` (`phone`,`product_id`,`description`,`rating`,`type`) VALUE ('$phone','$id','$feedback','$rating','1');";
        $conn->query($sql);
        $sql = "UPDATE `restaurant` SET `rating`=(`rating`*`total_rating`+'$rating')/(`total_rating`+1),`total_rating`=`total_rating`+1 WHERE `id` = '$id'";
        $conn->query($sql);
        $result['success']=true;
        $result['message']='Thank you for your feedback';
    }
    
}

echo json_encode($result);