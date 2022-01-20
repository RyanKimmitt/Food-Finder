<?php
$serverName = "localhost";
$userName= "Ryan";
$password="cool*99*";
$dbName = "food";

$conn = mysqli_connect($serverName, $userName, $password, $dbName);

if($conn -> connect_error){
    die("Connection failed: ". $conn->connect_error);
}