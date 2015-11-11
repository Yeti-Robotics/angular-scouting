<?php
include("connect.php");
include("functions.php");

if (isset($_GET["token"])) {
    die(json_encode(validateToken($db, $_GET["token"])));
} else {
    $db->close();
    die("Error: Invalid token");
}
?>