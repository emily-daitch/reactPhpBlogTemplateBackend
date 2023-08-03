<?php

namespace services;

include 'sharkweek.php'; // Lives outside repo / webroot
use mysqli;

class DB
{
    public function database()
    {
        global $servername, $username, $password, $database;
        // Making connection
        $conn = mysqli_connect($servername, $username, $password, $database);
        unset($servername, $username, $password, $database);
        
        // Checking connection
        if($conn->connect_error)
        {
            die("Connection failed ".$conn->connect_error);
        }

        return $conn;
    }
}
