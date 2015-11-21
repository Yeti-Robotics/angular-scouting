<?php
    include("connect.php");
    include("functions.php");

    $params = json_decode(file_get_contents('php://input'), true);
    if((!empty($params["id"])) && (!empty($params["pswd"]))) {
        getByteCoins($db, $params["id"], $params["pswd"]);
    }
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
?>
