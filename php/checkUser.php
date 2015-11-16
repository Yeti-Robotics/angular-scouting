<?php
    include("connect.php");
    include("functions.php");

    $params = json_decode(file_get_contents('php://input'), true);

    if(getName($db, $params["username"], md5($params["pswd"]))) {
        die(json_encode(array(
            "name" => getName($db, $params["username"], md5($params["pswd"])),
            "token" => startSession($db, $params["username"], md5($params["pswd"]))
        )));
    }
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
    die ("Invalid username/password");
?>
