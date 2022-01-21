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
$result['error']='0';

if(isset($_GET) && isset($_GET['phone'])  && isset($_GET['password'])){
    $ph = $_GET['phone'];
    $pass = $_GET['password'];
    
    $sql = "SELECT * FROM restaurant WHERE phone = '$ph' AND password = '$pass' LIMIT 1";
    $res = $conn->query($sql);
    if($res->num_rows==1){
        $index=array();
        $result['data']=array();
        $row = $res->fetch_assoc();
        $index['id']=$row['id'];
        $index['name']=$row['name'];
        $index['image']=$row['image'];
        $index['address']=$row['address'];
        $index['email']=$row['email'];
        $index['phone']=$row['phone'];
        $index['password']=$row['password'];
        $index['status']=$row['status'];
        $index['description']=$row['description'];
        $index['rating']=$row['rating'];
        $index['total_rating']=$row['total_rating'];
        $index['accno']=$row['accno'];
        $index['ifsc']=$row['ifsc'];
        $index['bank_name']=$row['bank_name'];
        $index['category']=$row['category'];
        
        $result['success']=true;
        if(isset($_GET['fcm'])){
            $fcm=$_GET['fcm'];
            $rid=$row['id'];
            $sq="UPDATE `restaurant` SET `fcm`='$fcm' WHERE `id` = '$rid' LIMIT 1;";
            $conn->query($sq);
            $index['fcm']=$fcm;
        }
		if(isset($_GET['loc']) && $_GET['loc']!=''){
            $location = $_GET['loc'];
            $rid=$row['id'];
            $sq="UPDATE `restaurant` SET `location`='$location' WHERE `id` = '$rid' LIMIT 1;";
            $conn->query($sq);
        }
        array_push($result['data'],$index);
    }
    else{
        $sql = "SELECT * FROM restaurant WHERE phone = '$ph' LIMIT 1";
        $res = $conn->query($sql);
        if($res->num_rows==1){
            $result['message']="Wrong Password";
            $result['error']='1';
        }
        else{
            $result['message']="Restaurant not registered";
            $result['error']='2';
        }
    }
}
echo json_encode($result);