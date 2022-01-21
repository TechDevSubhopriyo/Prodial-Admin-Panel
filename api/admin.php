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

if(isset($_GET) && isset($_GET['token'])){
  $token = $_GET['token'];
  $sql = "UPDATE admin SET fcm = '$token' LIMIT 1";
  if($conn->query($sql)){
    $result['success']=true;
  }
  goto print1;
}

print1:echo json_encode($result);

?>