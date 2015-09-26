<?php
    include("connect.php");
    include("SQLFunctions.php");

    echo getName($db, $_POST["id"], $_POST["pswd"]);
?>
