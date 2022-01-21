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
    
    $sql = "DELETE FROM `restaurant` WHERE `id`='$id' LIMIT 1";
    $conn->query($sql);
    $sql = "DELETE FROM `product` WHERE `restaurant`='$id'";
    $conn->query($sql);
    $result['success']=true;
    $result['message']="Deleted";
    goto print1;
}

if(isset($_POST) && isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['phone']) && isset($_POST['email'])&& isset($_POST['address'])&& isset($_POST['accno']) && isset($_POST['ifsc'])&& isset($_POST['bname'])&& isset($_POST['category'])){
    $id = $_POST['id'];
    $name=$_POST['name'];
    $description=$_POST['description'];
    $phone=$_POST['phone'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    $accno =$_POST['accno'];
    $ifsc=$_POST['ifsc'];
    $bname=$_POST['bname'];
    $category =$_POST['category'];
    
    $sql = "UPDATE `restaurant` SET `name`='$name',`description`='$description',`phone`='$phone',`email`='$email',`address`='$address',`accno`='$accno',`ifsc`='$ifsc',`bank_name`='$bname',`category`='$category'";
    
    if(isset($_FILES['file'])){
        $filename = $_FILES['file']['name'];
        $location = "../restaurant_image/".time().$filename;
        $img = $c->getHost()."restaurant_image/".time().$filename;
        
        move_uploaded_file($_FILES['file']['tmp_name'],$location);
        $sql.=",`image`='$img'";
    }
    $sql.=" WHERE `id`='$id' LIMIT 1";
    $result['q']=$sql;
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Updated";
    }
    else{
        $result['message']="Some error occurred";
    }
    goto print1;
}

if(isset($_POST)&& !isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['phone']) && isset($_POST['email'])&& isset($_POST['address'])&& isset($_POST['accno']) && isset($_POST['ifsc'])&& isset($_POST['bname'])&& isset($_POST['category'])){
    
    $name=$_POST['name'];
    $description=$_POST['description'];
    $phone=$_POST['phone'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    $accno =$_POST['accno'];
    $ifsc=$_POST['ifsc'];
    $bname=$_POST['bname'];
    $category =$_POST['category'];
    $lo =$_POST['location'];
    
    $filename = $_FILES['file']['name'];
    $location = "../restaurant_image/".time().$filename;
    $img = $c->getHost()."restaurant_image/".time().$filename;
    
    move_uploaded_file($_FILES['file']['tmp_name'],$location);
    
    $sql = "INSERT INTO `restaurant` (`id`, `name`, `image`, `phone`, `address`, `email`, `status`, `description`, `rating`, `total_rating`, `accno`, `ifsc`, `bank_name`, `category`,`password`, `location`) VALUES (NULL, '$name', '$img', '$phone', '$address', '$email', '0', '$description', '0.0', '0', '$accno', '$ifsc', '$bname', '$category','$phone','$lo');";
    if($conn->query($sql)){
        $result['success']=true;
        $result['message']="Inserted";
    }
    else{
        $result['message']="Some error occurred";
    }
    goto print1;
}
// if(isset($_GET) && isset($_GET['category_id'])){
//     $cid=','.$_GET['category_id'].',';
//     $sql = "SELECT * FROM restaurant ORDER BY rating DESC";
//     $res = $conn->query($sql);
//     if($res->num_rows>0){
//         $result['data']=array();
//         while($row=$res->fetch_assoc()){
//             $c = $row['category'];
//             $cat = ','.$c.',';
            
//             if(strpos($cat,$cid) !== false)
//             {
//                 $result['success']=true;
//                 $index=array();
//                 $index['id']=$row['id'];
//                 $index['name']=$row['name'];
//                 $index['image']=$row['image'];
//                 $index['phone']=$row['phone'];
//                 $index['email']=$row['email'];
//                 $index['address']=$row['address'];
//                 $index['status']=$row['status'];
//                 $index['description']=$row['description'];
//                 $index['rating']=$row['rating'];
//                 $index['total_rating']=$row['total_rating'];
//                 $index['accno']=$row['accno'];
//                 $index['ifsc']=$row['ifsc'];
//                 $index['bank_name']=$row['bank_name'];
//                 $index['category']=$row['category'];
        
//                 array_push($result['data'],$index);
//             }
            
//         }
//     }
//     goto print1;
// }

if(isset($_GET) && isset($_GET['category_id'])){
    $cid=','.$_GET['category_id'].',';
    $sql = "SELECT * FROM restaurant WHERE CONCAT(',',`category`,',') LIKE '%$cid%' ORDER BY rating DESC;";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $result['data']=array();
        while($row=$res->fetch_assoc()){
           
            $result['success']=true;
            $index=array();
            $index['id']=$row['id'];
            $index['name']=$row['name'];
            $index['image']=$row['image'];
            $index['phone']=$row['phone'];
            $index['email']=$row['email'];
            $index['address']=$row['address'];
            $index['status']=$row['status'];
            $index['description']=$row['description'];
            $index['rating']=$row['rating'];
            $index['total_rating']=$row['total_rating'];
            $index['accno']=$row['accno'];
            $index['ifsc']=$row['ifsc'];
            $index['bank_name']=$row['bank_name'];
            $index['category']=$row['category'];
            $latf= $_GET['lat'];
            $lngf= $_GET['lng'];
            $latlng = explode(',',$row['location']);
            $d = $c->distance($latf,$lngf,$latlng[0],$latlng[1]);
            $index['distance']=$d;
    
            if($d<=$c->getMinDistance()){
                array_push($result['data'],$index);
            }
        
        }
    }
    goto print1;
}

$sql = "SELECT * FROM `restaurant`";

// if(isset($_GET) && isset($_GET['restaurants'])){
//   $sql = "SELECT * FROM `restaurant` ORDER BY `name` ASC";
// }

$res = $conn->query($sql);
if($res->num_rows>0){
    $result['success']=true;
    $result['data']=array();
    while($row=$res->fetch_assoc()){
        $index=array();
        $index['id']=$row['id'];
        $index['name']=$row['name'];
        $index['image']=$row['image'];
        $index['phone']=$row['phone'];
        $index['email']=$row['email'];
        $index['address']=$row['address'];
        $index['status']=$row['status'];
        $index['description']=$row['description'];
        $index['rating']=$row['rating'];
        $index['total_rating']=$row['total_rating'];
        $index['accno']=$row['accno'];
        $index['ifsc']=$row['ifsc'];
        $index['bank_name']=$row['bank_name'];
        $index['category']=$row['category'];
        
        if(isset($_GET['restaurants']))
        {
            array_push($result['data'],$index);
        }
		if(isset($_GET['lat']) && isset($_GET['lng'])){
			$latf= $_GET['lat'];
			$lngf= $_GET['lng'];
			$rl = str_replace(' ', '', $row['location']);
			$latlng = explode(',',$rl);
			$d = $c->distance($latf,$lngf,$latlng[0],$latlng[1]);
			$index['distance']=$d;
            if($d<=$c->getMinDistance()){
                array_push($result['data'],$index);
            }
        }
    }
}
if(isset($_GET) && isset($_GET['phone']) && isset($_GET['password'])){
    $ph = $_GET['phone'];
    $pass=$_GET['password'];
    $sql = "SELECT * FROM restaurant WHERE `phone`='$ph' AND `password`='$pass' LIMIT 1;";
    $res = $conn->query($sql);
    if($res->num_rows==1){
        $result['success']=true;
        $result['data']=array();
        while($row=$res->fetch_assoc()){
            $index=array();
            $index['id']=$row['id'];
            $index['name']=$row['name'];
            $index['image']=$row['image'];
            $index['phone']=$row['phone'];
            $index['email']=$row['email'];
            $index['address']=$row['address'];
            $index['status']=$row['status'];
            $index['description']=$row['description'];
            $index['rating']=$row['rating'];
            $index['total_rating']=$row['total_rating'];
            $index['accno']=$row['accno'];
            $index['ifsc']=$row['ifsc'];
            $index['bank_name']=$row['bank_name'];
            $index['category']=$row['category'];
    
            array_push($result['data'],$index);
        }
    }
    else{
        $sql = "SELECT * FROM restaurant WHERE `phone`='$ph' LIMIT 1;";
        $res = $conn->query($sql);
        if($res->num_rows==1){
            $result['message']="Wrong Password";
        }
        else{
            $result['message']="No Restaurant found with the given number";
        }
    }
}
print1:echo json_encode($result);

?>