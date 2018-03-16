<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$teamNumber = $_GET['teamNumber'];

$query = "SELECT f.team_number, t.team_name, AVG(f.score) as avg_score, f.bar_climb, AVG(f.tele_check) as avg_climb,
	AVG(f.tele_speed) as avg_tele_speed, SUM(f.switch_cubes + f.enemy_switch_cubes + f.scale_cubes) AS total_cubes,
	SUM(f.auto_scale +f. auto_switch) AS total_auto_cubes,
	SUM(f.vault_cubes) AS total_vault
	FROM form_data f
	JOIN scouters s ON s.id = f.scouter_id
	LEFT JOIN team_info t ON t.team_number = f.team_number
	WHERE s.team_number = ?
	GROUP BY f.team_number";

$output = array();
if ($stmt = $db->prepare($query)) {
	$stmt->bind_param("i", $teamNumber);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
		while($row = $result->fetch_assoc()) {
			$output[] = $row;
		}
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
	die('{"error": "Failed to retrive team data, problem with query 1"}');
}

die(json_encode($output));

$db->close ();

?>
