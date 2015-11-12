<?php
    include("connect.php");
    include("functions.php");

    $params = json_decode(file_get_contents('php://input'), true);
    if(getName($db, $params["username"], md5($params["pswd"]))) {
        getByteCoins($db, $params["username"], md5($params["pswd"]));
    }
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
?>
