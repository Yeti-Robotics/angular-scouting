<?php
include("functions.php");
header("Content-Type: application/json");
//$matchResults = getMatchSchedule(); //For server
$matchResults = json_decode(file_get_contents("../json/NCMLMatchResults.json"), true)["Schedule"]; //For localhost
$uncompletedMatchs = array();
foreach($matchResults as $match) {
	if(empty($match["actualStartTime"])) {
		$uncompletedMatchs[] = $match;
	}
}
echo json_encode((array("Schedule" => $uncompletedMatchs)));
?>
