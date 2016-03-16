<?php
include("connect.php");
include("functions.php");

if (isset($_GET["token"])) {
    die(getSessionUser($db, $_GET["token"]));
} else {
    $db->close();
    die('{"error": "Invalid token"}');
}
?>