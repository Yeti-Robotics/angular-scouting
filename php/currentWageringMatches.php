<?php

include("functions.php");
include("connect.php");

$futureMatches = getFutureMatches($db);
if (getLastMatch($db) > 0) {
	$illegalMatch = array_splice($futureMatches, array_search(getLastMatch($db), $futureMatches), 1);
}

echo(json_encode(array("Schedule" => $futureMatches)));

?>
