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

$data = json_decode(file_get_contents("php://input"));
$oid = $data->order_id;
$tip = $data->tip;

$sql = "UPDATE orders SET tip='$tip' WHERE id='$oid' LIMIT 1;";
if($conn->query($sql)){
    $result['success']=true;
}
else{
    $result['message']="Some error occured";
}
echo json_encode($result);