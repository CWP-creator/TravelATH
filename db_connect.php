<?php
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL password
$dbname = "travel_agency_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// NO CLOSING ?> 