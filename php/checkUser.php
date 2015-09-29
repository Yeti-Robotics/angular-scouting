<?php
    include("connect.php");
    include("SQLFunctions.php");

    $params = json_decode(file_get_contents('php://input'), true);

    if((!empty($params["id"])) && (!empty($params["pswd"]))) {
        echo(getName($db, $params["id"], $params["pswd"]));
    }
echo false;
?>
