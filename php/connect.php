<?php
// connect to database using credentials in config.php
include("../config/config.php");
$db = new mysqli();
$db->connect($dbserver, $dbuser, $dbpassword, $dbname);
if (mysqli_connect_error()){
	echo("failure connecting to database");
}

?>
