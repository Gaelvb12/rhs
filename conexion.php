<?php

$host = "localhost";
$user = "root";
$password = ""; 
$database = "rh_system"; 


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

echo "";
?>
