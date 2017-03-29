<?php

include("connect.php");
include("functions.php");

$params = json_decode(file_get_contents('php://input'), true);

if (validateToken($db, $params["token"])) {
	die(json_encode(getSessionUser($db, $params["token"])));
} else {
	die(json_encode(false));
}

$db->close();

?>