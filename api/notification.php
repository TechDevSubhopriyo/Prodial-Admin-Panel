<?php
function notify($to,$notif){
    $apiKey = 'AAAA145DWMI:APA91bFDvVhL94OWNwEFVFIUOAKJqPLXHNBIqs1QEG2kyAneZnyhIkOUT5pSJKGN37V01YaOWJ57atTtspM9JqlpuYELCR0vy3rd4b-BAqo9Llr-AYvW6gT96ID2Xa3-UIOeRCwi2Vdy';
    $ch = curl_init();
    $field = json_encode(array('data'=>$notif,'to'=>$to,));
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($field));

    $headers = array();
    $headers[] = 'Authorization: key='.$apiKey;
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
}
    
$to='cFaZa5CeTcCNmVL_oeVix-:APA91bFTZP5v90cWIjGLJa3Z5QyTJHDrpBcVYtkrLLTu1bUEM7QElxH7TLEbQs9Ng92P63J5DenL0UDRqa5tIpYq5U3d0bbNZr4jidCwI6_16N-KzPsasCLsIPG1RyaMItMiEqNAil8E';
    
$notif = array(
        'title'=>'Notification',
        'body'=>'This is a test Notification sent by your backend dev!',
        'type'=>$_GET['type'],    // 1-> order, 2-> coupon, 3-> anything
        );
        

if(isset($_GET) && isset($_GET['login'])){
    $to=$_GET['login'];
    
    $notif = array(
            'title'=>'Login Successful',
            'body'=>'Welcome to Prodial Restaurant App',
            'type'=>3,    // 1-> order, 2-> coupon, 3-> anything
            );
            
    notify($to,$notif);
}

if(isset($_POST) && isset($_POST['title']) && isset($_POST['body']) && isset($_POST['phones']))
{
    header('Access-Control-Allow-Origin: *');
require __DIR__ . '/constants.php';
$c = new Connect();
$conn = $c->connect();

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result=array();
$result['success']=false;
    $phones=$_POST['phones'];
    $t=$_POST['title'];
    $b=$_POST['body'];
    
    $notif = array(
        'title'=>$t,
        'body'=>$b,
        'type'=>3,    // 1-> order, 2-> coupon, 3-> anything
        );
    
    $sql = "SELECT fcm FROM `user` WHERE phone IN $phones;";
    $res=$conn->query($sql);
    while($row=$res->fetch_assoc()){
        $to=$row['fcm'];
                
        notify($to,$notif);
        $result['success']=true;
    }
    echo json_encode($result);
}
?>