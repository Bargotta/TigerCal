<?php

// DEFINE ('DB_USER', 'root');
// DEFINE ('DB_PASSWORD', 'Iydsafttata96');
// DEFINE ('DB_HOST', 'localhost');
// DEFINE ('DB_NAME', 'website');

// $dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
// OR die('Could not connect to MySQL ' . 
//     mysql_error());

    // $dbc = mysql_connect("localhost", "root", "Iydsafttata96")
    // or die("Could not connect.");

    // mysql_select_db("website", $dbc) or die("Could not find db!");
?>

<?php
$servername = "localhost";
$username = "root"; //"tigercal";
$password = "Iydsafttata96"; //"4cPfycjbt:M2";
$database = "website"; //"tigercal_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>