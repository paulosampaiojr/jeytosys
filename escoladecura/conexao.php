<?php
$servername = "srv742.hstgr.io";
$username = "u970734089_ibbjetrosys";
$password = "Wtiloveya@2012";
$database = "u970734089_ibbjetrosys";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>