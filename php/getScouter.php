<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$scouterId = isset($_GET["scouterId"]) ? $_GET["scouterId"] : -1;
$comments = array();
$timestamps = array();
$names = array();
$matchNumber = array();
if ($scouterId) {
	if (checkForUserFromId($db, $scouterId)) {
		$response = array();

		//Scouter data query
		$query = "SELECT name, byteCoins FROM scouters WHERE id = ?";
		if ($stmt = $db->prepare($query)){
			$stmt->bind_param("i", $scouterId);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result) {
				while ($row = $result->fetch_assoc()) {
					$response['scouter']['name'] = $row['name'];
					$response['scouter']['bytecoins'] = $row['byteCoins'];
				}
			}
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
			die('{"error": "Failed to get scouter data"}');
		}

		//Comments query
		$query = "SELECT team_number, comments, UNIX_TIMESTAMP(timestamp) AS timestamp, match_number
				FROM scout_data
				WHERE name = ?";
		if ($stmt = $db->prepare($query)){
			$stmt->bind_param("s", $response['scouter']['name']);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result) {
				while ($row = $result->fetch_assoc()) {
					$row['timestamp'] = timeAgo($row['timestamp']);
					$response['comments'][] = $row;
				}
			}
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
			die('{"error": "Failed to get comments"}');
		}
		echo(json_encode($response));
	} else {
		header('HTTP/1.1 500 Internal Server Error', true, 500);
		die('{"error": "No Scouter data"}');
	}
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to retrive Scouter data"}');
}
?>
