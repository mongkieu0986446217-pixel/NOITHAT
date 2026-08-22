<?php
$SERVER ="localhost";
$USERNAME ="root";
$PASSWORD ="";
$DBNAME ="noithat";
$conn = new mysqli($SERVER, $USERNAME, $PASSWORD, $DBNAME);

if ($conn->connect_error) {
    die("Kết nối thất bại");
}
?>
