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
    $sql = "SELECT * FROM rider WHERE phone = '$ph' AND password = '$pass' LIMIT 1";
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
        $index['review']=$row['review'];
        $index['status']=$row['status'];
        $index['geo_location']=$row['geo_location'];
        $index['phone']=$row['phone'];
        
        array_push($result['data'],$index);
        $result['success']=true;
    }
    else{
        $sql = "SELECT * FROM rider WHERE phone = '$ph' LIMIT 1";
        $res = $conn->query($sql);
        if($res->num_rows==1){
            $result['message']="Wrong Password";
            $result['error']='1';
        }
        else{
            $result['message']="Rider not registered";
            $result['error']='2';
        }
    }
}
if(isset($_GET) && isset($_GET['phone']) && !isset($_GET['password'])){
    $phone=$_GET['phone'];
    $sql = "SELECT * FROM rider WHERE phone='$phone' LIMIT 1";
    $res = $conn->query($sql);
    if($res->num_rows==1){
        $result['success']=true;
    }
}
if(isset($_GET) && isset($_GET['riders'])){
    $sql = "SELECT * FROM rider WHERE status='1';";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $result['data']=array();
        $index=array();
        $result['success']=true;
        while($row=$res->fetch_assoc()){
            $index['id']=$row['id'];
            $index['phone']=$row['phone'];
            $index['name']=$row['name'];
            $index['address']=$row['address'];
            $index['image']=$row['image'];
            $index['email']=$row['email'];
            $index['review']=$row['review'];
            $index['status']=$row['status'];
            $index['proof']=$row['photo_id'];
            
            array_push($result['data'],$index);
            $result['success']=true;
        }
    }
}
if(isset($_GET) && isset($_GET['rider'])){
    $sql = "SELECT * FROM rider ORDER BY name ASC;";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $result['data']=array();
        $index=array();
        $result['success']=true;
        while($row=$res->fetch_assoc()){
            $index['id']=$row['id'];
            $index['phone']=$row['phone'];
            $index['name']=$row['name'];
            $index['address']=$row['address'];
            $index['image']=$row['image'];
            $index['email']=$row['email'];
            $index['review']=$row['review'];
            $index['status']=$row['status'];
            $index['proof']=$row['photo_id'];
            
            array_push($result['data'],$index);
            $result['success']=true;
        }
    }
}
if(isset($_POST)  && !isset($_POST['id']) && isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['email'])  && isset($_POST['address'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address= $_POST['address'];
    $review= $_POST['review'];
    
    $filename = $_FILES['file']['name'];
    $location = "../rider_image/".time().$filename;
    $img = $c->getHost()."rider_image/".time().$filename;
    
    move_uploaded_file($_FILES['file']['tmp_name'],$location);
    
    $sql = "INSERT INTO `rider` (`id`,`name`,`phone`,`email`,`address`,`password`,`photo_id`,`review`,`status`) VALUES (NULL,'$name','$phone','$email','$address','$phone','$img','$review','0');";
    
    if($conn->query($sql))
    {
        $result['success']=true;
    }
    else{
        $result['message']=false;
    }
}

if(isset($_POST) && isset($_POST['id']) && isset($_POST['geo_location']))
{
    $g=$_POST['geo_location'];
    $id = $_POST['id'];
    $sql= "UPDATE `rider` SET `geo_location`='$g' ";
    $sql.=" WHERE `id`= '$id' LIMIT 1;";
    
    if($conn->query($sql))
    {
        $result['success']=true;
        $result['message']="Location Updated";
    }
    else{
        $result['message']="Some error Occured";
    }
}
if(isset($_POST) && isset($_POST['id']) && isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['email'])  && isset($_POST['address'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address= $_POST['address'];
    $id = $_POST['id'];
    
    $sql = "UPDATE `rider` SET `name`='$name',`phone`='$phone',`email`='$email',`address`='$address'  ";
    if(isset($_POST['geo_location']))
    {
        $g=$_POST['geo_location'];
        $sql= "UPDATE `rider` SET `geo_location`='$g' ";
    }
    if(isset($_FILES['file']))
    {
        $filename = $_FILES['file']['name'];
        $location = "../rider_image/".time().$filename;
        $img = $c->getHost()."rider_image/".time().$filename;
        
        move_uploaded_file($_FILES['file']['tmp_name'],$location);
        $sql.=",`photo_id`='$img' ";
    }
    if(isset($_POST['review'])){
        $r=$_POST['review'];
        $sql.=",`review`='$r' ";
    }
    if(isset($_POST['image'])){
        $image=$_POST['image'];
        $sql.=",`image`='$image' ";
    }
    $sql.=" WHERE `id`= '$id' LIMIT 1;";
    
    if($conn->query($sql))
    {
        $result['success']=true;
        $result['message']="Profile Updated";
    }
    else{
        $result['message']="Some error Occured";
    }
}

if(isset($_POST) && isset($_POST['delete'])){
    $id =$_POST['delete'];
    $sql = "DELETE FROM `rider` WHERE `id` = '$id' LIMIT 1;";
    $conn->query($sql);
    $result['success']=true;
}

echo json_encode($result);