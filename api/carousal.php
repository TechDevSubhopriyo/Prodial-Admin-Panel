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

if(isset($_POST) && isset($_POST['delete'])){
  $id = $_POST['delete'];
  $sql = "DELETE FROM `carousal` WHERE `id`='$id' LIMIT 1";
  if($conn->query($sql)){
    $result['success']=true;
  }
  goto print1;
}

if(isset($_POST) && isset($_FILES['file'])){
  
  $filename = $_FILES['file']['name'];
  $fname = time().$filename;
  $location = "../restaurant_image/".$fname;
  $img = $c->getHost()."restaurant_image/".$fname;
    
  move_uploaded_file($_FILES['file']['tmp_name'],$location);
  
  $sql = "INSERT INTO carousal (`image`) VALUES ('$img');";
  if($conn->query($sql)){
    $result['success']=true;
  }
  goto print1;
}

$sql = "SELECT * FROM `carousal` ORDER BY `id` DESC";
$res = $conn->query($sql);
$result['data']=array();
$result['carousel']=array();
$index=array();
$index2=array();

while($row=$res->fetch_assoc()){
    $result['success']=true;
    $index2['id']=$row['id'];
    $index2['image']=$row['image'];
    array_push($index,$row['image']);
    array_push($result['carousel'],$index2);
}
$result['data']=$index;
print1:echo json_encode($result);

?>