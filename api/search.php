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

if(isset($_GET) && isset($_GET['query'])){
    $q=$_GET['query'];
    $index=array();
    $result['data']=array();
    $sql="SELECT id,name,image,location FROM restaurant WHERE name LIKE '%$q%' AND status='1';";
    $res = $conn->query($sql);
    if($res->num_rows>0)
    {
        while($row=$res->fetch_assoc()){
            $index['id']=$row['id'];
            $index['name']=$row['name'];
            $index['image']=$row['image'];
            $index['type']=0; //Type 0 for restaurant
            $loc = explode(",",$row['location']);
            
            $lat = $_GET['lat'];
            $lng = $_GET['lng'];
            $d = $c->distance($lat,$lng,$loc[0],$loc[1]);
            
            if($d<=$c->getMinDistance())
            {
                array_push($result['data'],$index);
            }
            
            $result['success']=true;
        }
    }
    $sql="SELECT id,name,image,restaurant FROM product WHERE name LIKE '%$q%';";
    $res = $conn->query($sql);
    if($res->num_rows>0)
    {
        while($row=$res->fetch_assoc()){
            $index['id']=$row['id'];
            $index['name']=$row['name'];
            $index['image']=$row['image'];
            $index['type']=1; //Type 0 for restaurant
            
            $res1 = $row['restaurant'];
            $rs = "SELECT * FROM restaurant WHERE id = '$res1';";
            $ress = $conn->query($rs);
            $rw = $ress->fetch_assoc();
            $loc = explode(",",$rw['location']);
            $d = $c->distance($_GET['lat'],$_GET['lng'],$loc[0],$loc[1]);
            if($d<=$c->getMinDistance())
            {
                array_push($result['data'],$index);
            }
            
            $result['success']=true;
        }
    }
}
echo json_encode($result);