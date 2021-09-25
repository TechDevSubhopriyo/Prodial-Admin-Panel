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

if(isset($_GET) && isset($_GET['user'])){
  $sql = "SELECT * FROM `user` ORDER BY `username` ASC";
  $res = $conn->query($sql);
  $index=array();
  $result['data']=array();  
  while($row=$res->fetch_assoc()){
    $index['image']=$row['image'];
    $index['phone']=$row['phone'];
    $index['name']=$row['username'];
    $index['email']=$row['email'];
    $index['address']=$row['address'];
    $index['refid']=$row['refid'];
    $index['refby']=$row['refby'];
    $index['created']=$row['created'];
    $result['success']=true;
    array_push($result['data'],$index);
  }
  goto print1;
}
if(isset($_GET) && isset($_GET['delete'])){
    $phon=$_GET['delete'];
    $sql = "DELETE FROM `user` WHERE `phone` = '$phon' LIMIT 1;";
    $conn->query($sql);
    $result['success']=true;
    goto print1;
}

$result['error']=0;
$result['message']='';

$data = json_decode(file_get_contents("php://input"));
$action = $data->action;

function random_strings($length_of_string) 
{ 
    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle($str_result), 0, $length_of_string); 
} 


if($action=="0"){          //Action 0 for registration
  $phone = $data->phone;
  $pass = $data->password;
  $name = $data->name;
  $fcm = $data->fcm;
  $ref = $data->ref;

  $sql = "SELECT * FROM user WHERE phone = '$phone' LIMIT 1";
  $res = $conn->query($sql);
  if($res->num_rows==1){
    $result['error']=1; // User already exist
    $result['message']="User already exist";
    goto print1;
  }
  $rby ='';
  if(strlen($ref)>0){
      $ss = "SELECT * FROM `user` WHERE `refid` = '$ref' LIMIT 1";
      $ress = $conn->query($ss);
      if($ress->num_rows==1){
          $rw = $ress->fetch_assoc();
          $rby = $rw['username'];
          $rbyph = $rw['phone'];;
          
            $sql = "SELECT * FROM `admin` LIMIT 1";
            $res = $conn->query($sql);
            $row = $res->fetch_assoc();
            $rcid=$row['refer_coupon_id'];
            $sql = "INSERT INTO `coupon_assign` (`id`, `coupon_id`, `user_id`) VALUES (NULL, '$rcid', '$phone');";
            $conn->query($sql);
            $sql = "INSERT INTO `coupon_assign` (`id`, `coupon_id`, `user_id`) VALUES (NULL, '$rcid', '$rbyph');";
            $conn->query($sql);
      }
      else{
            $result['error']=3; 
            $result['message']="Invalid Referral ID";
            goto print1;
      }
  }

  $rc = random_strings(6).substr($phone,6);
  $sql = "INSERT INTO user (`phone`,`password`,`username`,`fcm`,`refid`,`refby`,`image`) VALUES ('$phone','$pass','$name','$fcm','$rc','$rby','data:image/webp;base64,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');";
  if($conn->query($sql)){
    $result['success']=true;
    $result['refId']=$rc;
    $result['message']="Registration Successfull";
  }
  else{
    $result['error']=2; //Some error occurred
    $result['message']="Some unknown error occurred";
  }
}
if($action=="1"){         //Action 1 for Update Account
  $phone = $data->phone;
  $name = $data->name;
  $fcm = $data->fcm;
  $image = $data->image;
  $email = $data->email;
  $adr = $data->address;

  $sql = "UPDATE user SET username='$name', fcm='$fcm', image='$image',email='$email',address='$adr' WHERE phone = '$phone' LIMIT 1";
  
  if($conn->query($sql)){
    $result['success']=true;
    $result['message']="Profile Updated";
  }
  else{
    $result['error']=1; //Some error occurred
    $result['message']="Some unknown error occurred";
  }
}
if($action=="2"){         //Action 2 for Login
  $phone = $data->phone;
  $pass = $data->password;

  $sql = "SELECT * FROM user WHERE `phone` = '$phone' AND `password` = '$pass' LIMIT 1";
  $res = $conn->query($sql);

  $sql1 = "SELECT * FROM user WHERE `phone` = '$phone' LIMIT 1";
  $res1 = $conn->query($sql1);

  if($res->num_rows==1){
    $user = array();
    $row = $res->fetch_assoc();
    $user['phone']=$row['phone'];
    $user['name']=$row['username'];
    $user['email']=$row['email'];
    $user['image']=$row['image'];
    $user['address']=$row['address'];
    $user['fcm']=$row['fcm'];
    $user['refid']=$row['refid'];
    $user['refby']=$row['refby'];
    $result['data']=($user);

    $result['success']=true; //Login Successful
    $result['message']="Login Successful";
  }
  else if($res1->num_rows==1){
    $result['error']=1; //Wrong Password
    $result['message']="Wrong Password";
  }
  else if($res->num_rows==0){
    $result['error']=2; //User not registered
    $result['message']="User not registered";
  }
  else{
    $result['error']=3; //Some error occurred
    $result['message']="Some unknown error occurred";
  }
}
if($action=="3"){
  $phone = $data->phone;

  $sql = "SELECT * FROM user WHERE phone='$phone' LIMIT 1";
  $res = $conn->query($sql);
  if($res->num_rows==1){
    $result['success']=true;
  }
}
if($action=="4"){          //Action 4 for pass reset
  $phone = $data->phone;
  $pass = $data->password;
  $sql = "UPDATE user SET password='$pass' WHERE phone='$phone' LIMIT 1;";
  if($conn->query($sql)){
      $result['success']=true;
      $result['message']="Password Updated!";
  }
  else{
      $result['message']="Some Error Ocuured!";
  }
  goto print1;
}
print1:echo json_encode($result);
?>