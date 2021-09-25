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

$data = json_decode(file_get_contents("php://input"));
if(isset($_POST) && isset($_POST['delete'])){
    $id = $_POST['delete'];
    $sql = "DELETE FROM `product` WHERE `id` = '$id' LIMIT 1";
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Updated Product";
    }
    goto print1;
}
if(isset($_POST) && isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['offer_price']) && isset($_POST['stock'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $offer_price = $_POST['offer_price'];
    $desc = $_POST['description'];
    
    $sql = "UPDATE `product` SET `name`='$name',`stock`='$stock',`price`='$price',`offer_price`='$offer_price',`description`='$desc'";
    if(isset($_FILES['file'])){
        $filename = $_FILES['file']['name'];
        $location = "../restaurant_image/".time().$filename;
        $img = $c->getHost()."restaurant_image/".time().$filename;
        
        move_uploaded_file($_FILES['file']['tmp_name'],$location);
        $sql .= ",`image`='$img'";
    }
    $sql .=" WHERE `id`='$id' LIMIT 1";
    
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Updated Product";
    }
    
    goto print1;
    
}

if(isset($_POST) && !isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['offer_price']) && isset($_POST['stock']) && isset($_POST['restaurant'])&& isset($_POST['category'])){
    
    $name = $_POST['name'];
    $price = $_POST['price'];
    $rid = $_POST['restaurant'];
    $cid = $_POST['category'];
    $stock = $_POST['stock'];
    $offer_price = $_POST['offer_price'];
    $desc = $_POST['description'];
    
    $filename = $_FILES['file']['name'];
    $location = "../restaurant_image/".time().$filename;
    $img = $c->getHost()."restaurant_image/".time().$filename;
    
    move_uploaded_file($_FILES['file']['tmp_name'],$location);
    
    $sql = "INSERT INTO `product` (`name`,`image`,`stock`,`price`,`offer_price`,`restaurant`,`category`,`description`) VALUES ('$name','$img','$stock','$price','$offer_price','$rid','$cid','$desc');";
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Inserted Products";
        $sql2 = "SELECT * FROM `restaurant` WHERE `id` = '$rid' LIMIT 1";
        $re2 = $conn->query($sql2);
        $ro2 = $re2->fetch_assoc();
        $cats = ','.$ro2['category'].',';
        $cid2=','.$cid.',';
        if(strpos($cats,$cid2) === false){
            $cid = $ro2['category'].','.$cid;
            $ssql="UPDATE `restaurant` SET `category` = '$cid' WHERE `id` = '$rid' LIMIT 1";
            $conn->query($ssql);
        }
    }
    
    goto print1;
    
}

$result['data']=array();

if(isset($_GET) && isset($_GET['product'])){
  $sql = "SELECT * FROM `product` ORDER BY `name` ASC";
}
else{
    $rid = $data->restaurantId;
    $cid = $data->categoryId;
    $sql = "SELECT * FROM `product` WHERE `restaurant` = '$rid' AND `category` = '$cid'";
    if($cid==""){
        $sql = "SELECT * FROM `product` WHERE `restaurant` = '$rid'";
    }
}
$res = $conn->query($sql);
if(mysqli_num_rows($res)>0){
    $index=array();
    while($row=$res->fetch_assoc()){
        $index['id']=$row['id'];
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
    }
    $result['success']=true;
}

print1:echo json_encode($result);