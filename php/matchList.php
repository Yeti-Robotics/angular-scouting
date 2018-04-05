<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$matches = array_slice(getMatchSchedule(), 0, getLastMatch($db));

die(json_encode($matches));

?>
