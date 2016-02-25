<?php

include("connect.php");
include("functions.php");

$teamNumber = $_GET["teamNumber"];

die(json_encode(getTeamInfo($db, $teamNumber)));
?>
