<?php

include("connect.php");
include("functions.php");

$teamNumber = $_GET["teamNumber"];

die(getTeamInfo($teamNumber));
?>