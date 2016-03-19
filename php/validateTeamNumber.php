<?php

include('functions.php');

$teamNumber = json_decode(file_get_contents('php://input'), true);
$fileName = "../json/" . $tournamentKey . "MatchResults.json";
$schedule = getMatchData();
$teams = array();

foreach ($schedule as $match) {
	for ($i = 0; $i < 6; $i++) {
		$teams[] = $match["Teams"][$i]["teamNumber"];
	}
}

if (array_search($teamNumber, $teams)) {
	die();
} else {
	header('HTTP/1.1 Team Invalid', true, 500);
	die();
}

?>