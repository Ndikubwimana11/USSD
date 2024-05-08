<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "uzima";

try{

    $conn = new PDO("mysql:host=localhost;dbname=uzima", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){

    echo "connection failed", $e->getMessage();



}



?>