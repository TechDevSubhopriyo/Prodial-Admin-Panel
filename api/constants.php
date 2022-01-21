<?php

class Connect {
    public $servername;
    public $username;
    public $password;
    public $dbname;
    public $host;
    public $conn;
    public $minDistance;

    function __construct() {
        $this->servername = "167.86.109.19";
        $this->username = "prodiale_database_user";
        $this->password = "ptD80c6@";
        $this->dbname = "prodiale_database";
        $this->host= "https://prodialexpress.com/";
        $this->minDistance = 15;
    }

    function connect(){
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        return $this->conn;
    }
    
    function getHost(){
        return $this->host;
    }
    
    function getMinDistance(){
        return $this->minDistance;
    }
    
    function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        
        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        $angle = atan2(sqrt($a), $b);
        
        return ($angle * $earthRadius);
    }

}

?>