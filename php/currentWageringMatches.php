<?php

include("functions.php");
include("connect.php");

$futureMatches = getFutureMatches($db);
$illegalMatch = array_splice($futureMatches, 1, 1);
echo(json_encode(array("Schedule" => $futureMatches)));

?>
