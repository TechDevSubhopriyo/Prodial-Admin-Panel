<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$r = array();
$r['success'] = false;

if(isset($_GET) && isset($_GET['user']) && isset($_GET['pass'])){
    $u = $_GET['user'];
    $p = $_GET['pass']; 
    $sql = "SELECT * FROM `admin` WHERE `username` ='$u' AND `password` = '$p' LIMIT 1";
    $result = $conn->query($sql);
    if($result->num_rows==1){
        $r['data']=array();
        $index = array();
        $r['success']=true;
        $row = $result->fetch_assoc();
        $index['username'] = $row['username'];
        $index['password'] = $row['password'];
        
        array_push($r['data'],$index);
    }
    else{
        $r['success']=false;
        $r['message']="Wrong Credentials!";
    }
}

echo json_encode($r);