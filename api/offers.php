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

$result['data']=array();

if(isset($_GET)){
    $sql = "SELECT * FROM `product` ORDER BY `name` ASC";
    
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
            
            if($row['offer_price']!=''){
                $op = floatval($row['offer_price']);
                $p = floatval($row['price']);
                if(($p-$op)/$p>=0.1){
                    array_push($result['data'],$index);
                    $result['success']=true;
                }
                else if(isset($_GET['offer'])){
                    array_push($result['data'],$index);
                    $result['success']=true;
                }
            }
        }
    }
}

print1:echo json_encode($result);