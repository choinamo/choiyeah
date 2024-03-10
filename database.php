<?php

$hostname = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "carino_db";

$conn = mysqli_connect($hostname, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("something went wrong, please try again later.");
}
?>