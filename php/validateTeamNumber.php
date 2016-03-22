<?php

include('functions.php');
include('../config/config.php');

$teamNumber = json_decode(file_get_contents('php://input'), true);
$fileName = "../json/" . $tournamentKey . "MatchResults.json";
$schedule = getMatchSchedule();
$teams = array();

foreach ($schedule as $match) {
	for ($i = 0; $i < 6; $i++) {
		$teams[] = $match["Teams"][$i]["teamNumber"];
	}
}

if (!array_search($teamNumber, $teams)) {
	header('HTTP/1.1 500 Team Invalid', true, 500);
}

?>
