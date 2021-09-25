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

if(isset($_POST) && isset($_POST['name']) && !isset($_POST['id'])){
  $name = $_POST['name'];
  
  $filename = $_FILES['file']['name'];
  $location = "../restaurant_image/".time().$filename;
  $img = $c->getHost()."restaurant_image/".time().$filename;
    
  move_uploaded_file($_FILES['file']['tmp_name'],$location);
    
  $sql = "INSERT INTO category (`name`,`image`) VALUES ('$name','$img');";
  if($conn->query($sql)){
    $result['success']=true;
  }
  goto print1;
}
if(isset($_POST) && isset($_POST['name']) && isset($_POST['id'])){
  $name = $_POST['name'];
  $id= $_POST['id'];
  $sql = "UPDATE category SET `name` = '$name'";
  if(isset($_FILES['file'])){
      $filename = $_FILES['file']['name'];
      $location = "../restaurant_image/".time().$filename;
      $img = $c->getHost()."restaurant_image/".time().$filename;
      
      move_uploaded_file($_FILES['file']['tmp_name'],$location);
      
      $sql .= ",`image` = '$img'";
  }
  $sql .= " WHERE `id` = '$id' LIMIT 1";

  if($conn->query($sql)){
    $result['success']=true;
  }
  goto print1;
}
if(isset($_POST) && isset($_POST['delete'])){
  $id= $_POST['delete'];
  
  $sql = "DELETE FROM `category` WHERE `id`='$id' LIMIT 1";

  if($conn->query($sql)){
    $result['success']=true;
  }
  $sql = "DELETE FROM `product` WHERE `category`='$id'";
  $conn->query($sql);
  goto print1;
}

$sql = "SELECT * FROM `category` ORDER BY `name` ASC";
$res = $conn->query($sql);

if($res->num_rows>0){
    $result['data']=array();
    $index=array();
    $result['success']=true;
    while($row=$res->fetch_assoc()){
        $index['id']=$row['id'];
        $index['image']=$row['image'];
        $index['name']=$row['name'];
        array_push($result['data'],$index);
    }
}
print1:echo json_encode($result);

?>