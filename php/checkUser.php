<?php
    include("connect.php");
    include("SQLFunctions.php");

    if((!empty($_POST["id"])) && (!empty($_POST["pswd"]))) {
        $name = "" + getName($db, $_POST["id"], $_POST["pswd"]);
        $db->close();
        if (!empty($name)) {
            die($name);
        }
    }
echo false;
?>
