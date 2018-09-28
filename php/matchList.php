<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$matches = getMatchSchedule();
$lastMatch = getLastMatch($db);
$qualMatches = array();

die(json_encode([
    "matches" => $matches,
    "lastMatch" => $lastMatch
]));

?>
