<?php
include("connect.php");
include("functions.php");

$params = json_decode(file_get_contents('php://input'), true);

deleteToken($db, $params["token"]);

?>
