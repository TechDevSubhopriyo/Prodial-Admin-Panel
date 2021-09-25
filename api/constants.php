<?php

class Connect {
    public $servername;
    public $username;
    public $password;
    public $dbname;
    public $host;
    public $conn;

    function __construct() {
        $this->servername = "156.67.222.127";
        $this->username = "u376419006_prodial";
        $this->password = "Vamshikrishna123@";
        $this->dbname = "u376419006_prodial";
        $this->host= "https://prodial.dtechblr.in/";
    }

    function connect(){
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        return $this->conn;
    }
    
    function getHost(){
        return $this->host;
    }

}

?>