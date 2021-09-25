<?php
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
date_default_timezone_set('Asia/Kolkata');
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result=array();
$result['success']=false;

if(isset($_GET) && isset($_GET['id'])){
    $ids = explode(",",$_GET['id']);
    if(($_GET['id'])==''){
        goto print1;
    }
    $result['data']=array();
    foreach($ids as $id){
        $index=array();
        $sql = "SELECT * FROM product WHERE `id`='$id' LIMIT 1";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $index['id']=$id;
        $index['name']=$row['name'];
        $index['image']=$row['image'];
        $index['stock']=$row['stock'];
        $index['rating']=$row['rating'];
        $index['total_rating']=$row['total_rating'];
        $index['price']=$row['price'];
        $index['offer_price']=$row['offer_price'];
        $index['restaurant']=$row['restaurant'];
        $index['category']=$row['category'];
        $index['description']=$row['description'];
        $r = $row['restaurant'];
        $c = $row['category'];
        $sql2 = "SELECT * FROM `restaurant` WHERE `id` = '$r' LIMIT 1";
        $res2 = $conn->query($sql2);
        $row2=$res2->fetch_assoc();
        $index['restaurant_name']=$row2['name'];
        $index['restaurant_image']=$row2['image'];
        $sql2 = "SELECT * FROM `category` WHERE `id` = '$c' LIMIT 1";
        $res2 = $conn->query($sql2);
        $row2=$res2->fetch_assoc();
        $index['category_name']=$row2['name'];

        array_push($result['data'],$index);
        $result['success']=true;
    }
}
print1: echo json_encode($result);