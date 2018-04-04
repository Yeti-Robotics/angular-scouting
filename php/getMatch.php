<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$matchNumber = $_GET['matchNumber'];
$scoutingTeam = $_GET['scoutingTeam'];

$response = array();
if ($matchNumber) {
	$query = "SELECT f.*, s.name, s.team_number AS scouting_team
	FROM form_data f
	LEFT JOIN scouters s ON s.id = f.scouter_id
	WHERE f.match_number = ? AND s.team_number = ?";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $matchNumber, $scoutingTeam);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['matches'][] = $row;
			}
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query"}');
	}
	echo(json_encode($response));

} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to retrive team data"}');
}


?>
