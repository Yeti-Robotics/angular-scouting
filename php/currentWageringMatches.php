<?php
include("functions.php");
header("Content-Type: application/json");
//$matchResults = getMatchSchedule();
$matchResults = json_decode(file_get_contents("../json/NCMCLMatchResults.json"), true);
$uncompletedMatchs = array();
foreach($matchResults as $match) {
	if(empty($match["actualStartTime"])) {
		$uncompletedMatchs[] = $match;
	}
}
echo json_encode((array("Schedule" => $uncompletedMatchs)));
?>
