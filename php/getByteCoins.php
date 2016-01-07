<?php
    include("connect.php");
    include("functions.php");

    $postData = json_decode(file_get_contents('php://input'), true);
    if(validateToken($db, $postData["token"])) {
        getByteCoins($db, $postData["token"]);
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
    }
?>
