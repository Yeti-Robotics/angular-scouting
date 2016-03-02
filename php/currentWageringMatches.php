<?php
include("functions.php");
header("Content-Type: application/json");
$matchResults = getMatchSchedule();
$uncompletedMatchs = array();
foreach($matchResults as $match) {
//	if(empty($match["actualStartTime"])) {
		$uncompletedMatchs[] = $match;
//	}
}
echo json_encode((array("Schedule" => $uncompletedMatchs)));
?>
