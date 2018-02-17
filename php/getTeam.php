<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : -1;
$response = array();
if ($teamNumber) {
	$query = "SELECT f.*, s.name
		FROM form_data f
		LEFT JOIN scouters s ON s.id = f.scouter_id
		WHERE f.team_number = ?";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
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

	$query = "SELECT AVG(auto_speed) AS avg_auto_speed, 
		AVG(tele_speed) AS avg_tele_speed,
		AVG(cube_ranking) AS avg_cube_ranking,
		SUM(switch_cubes) AS total_switch_cubes,
		SUM(scale_cubes) AS total_scale_cubes,
		SUM(enemy_switch_cubes) AS total_enemy_switch_cubes,
		SUM(switch_cubes + enemy_switch_cubes + scale_cubes) AS total_cubes,
		AVG(bar_climb)*100 AS climb_accuracy,
		AVG(auto_defend)*100 AS avg_auto_defend,
		AVG(tele_check)*100 AS avg_tele_check,
		AVG(auto_check)*100 AS avg_auto_check,
		AVG(help_climb)*100 AS avg_help_climb,
		AVG(ramp_climb)*100 AS avg_ramp_climb,
		AVG(tele_defense)*100 AS avg_tele_defense
	FROM form_data
	WHERE team_number = ?
	GROUP BY team_number
	";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
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

	echo(json_encode($response));

} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to retrive team data"}');
}
?>
