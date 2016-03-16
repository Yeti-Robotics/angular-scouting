<?php
include('connect.php');
include('functions.php');
header('Content-Type: application/json');

$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : 0;

if ($teamNumber) {
	if (checkPitData($db, $teamNumber)) {
		$response = array();
		$result = getPitComments($db, $teamNumber);
		if ($result) {
			while ($row = $result->fetch_assoc()) {
				if ($row["Pit Scouters Comments"] != "" && $row["Pit Scouters Comments"] != null) {
					$row['timestamp'] = timeAgo($row['timestamp']);
					$response['commentSection'][] = $row;
				}
			}
		}

		$result = getPicInfo($db, $teamNumber);
		if ($result) {
			while ($row = $result->fetch_assoc()) {
				$row['timestamp'] = timeAgo($row['timestamp']);
				$response['pics'][] = $row;
			}
		}
		
		$response['teamInfo'] = getTeamInfo($db, $teamNumber);
		
		die(json_encode($response));
	} else {
		header('HTTP/1.1 500 Internal Server Error', true, 500);
		die('{"error": "No team data"}');
	}
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to upload points"}');
}
?>