<?php
//$host = "feenix-mariadb.swin.edu.au";
//$user = "s102953892"; // your user name
//$pswd = "210901"; // your password
//$dbnm = "s102953892_db"; // your database

$host = "localhost";
$user = "root";
$pswd = "";
$dbnm = "s102953892_db";

//connect to the mysql server
$conn = mysqli_connect($host, $user, $pswd)
  or die("<p>Connection failed\n" . "Error Code: "
    . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");

//use database or create new
if (!@mysqli_select_db($conn, $dbnm)) {
  $SQLstring = "CREATE DATABASE $dbnm;";
  $QueryResult = @mysqli_query($conn, $SQLstring)
    or die("<p>The database server is not available.\n</p>"
    . "<p>Error code: " . mysqli_errno($conn) . ": " . mysqli_error($conn) . "</p>");
  echo "<p>You are the first visitor!</p>";
  mysqli_select_db($conn, $dbnm);
}
?>
