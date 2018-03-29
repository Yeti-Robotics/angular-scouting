<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : -1;
$scoutingTeam = $_GET['scoutingTeam'];

$response = array();
if ($teamNumber) {
	$query = "SELECT f.*, s.name, s.team_number AS scouting_team
	FROM form_data f
	LEFT JOIN scouters s ON s.id = f.scouter_id
	WHERE f.team_number = ? AND s.team_number = ?";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $teamNumber, $scoutingTeam);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['formData'][] = $row;
			}
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query 1"}');
	}

	$query = "SELECT * FROM team_info WHERE team_number = ?";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['team'] = $row;
			}
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query 2"}');
	}

	$query = "SELECT SUM(f.switch_cubes) AS total_switch_cubes,
		SUM(f.scale_cubes) AS total_scale_cubes,
		SUM(f.enemy_switch_cubes) AS total_enemy_switch_cubes,
		SUM(f.switch_cubes + enemy_switch_cubes + scale_cubes) AS total_cubes,
		SUM(f.vault_cubes) AS total_vault,
		SUM(f.auto_scale) AS total_auto_scale, 
		SUM(f.auto_switch) AS total_auto_switch,
		AVG(f.bar_climb)*100 AS climb_accuracy,
		AVG(f.tele_check)*100 AS avg_tele_check,
		AVG(f.auto_check)*100 AS avg_auto_check,
		AVG(f.help_climb)*100 AS avg_help_climb,
		AVG(f.ramp_climb)*100 AS avg_ramp_climb,
		AVG(f.tele_defense)*100 AS avg_tele_defense,
		s.team_number AS scouting_team
	FROM form_data f
	LEFT JOIN scouters s ON s.id = f.scouter_id
	WHERE f.team_number = ? AND s.team_number = ?
	GROUP BY f.team_number 
	";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $teamNumber, $scoutingTeam);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['stats'] = $row;
			}
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query 3"}');
	}

	$query = "SELECT tele_cube_stack
	FROM form_data f
	LEFT JOIN scouters s ON s.id = f.scouter_id
	WHERE f.team_number = ? AND s.team_number = ?
	ORDER BY tele_cube_stack
	LIMIT 1
	";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $teamNumber, $scoutingTeam);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['stats']['tele_cube_stack'] = $row['tele_cube_stack'];
			}
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query 4"}');
	}

	echo(json_encode($response));

} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to retrive team data"}');
}


?>
