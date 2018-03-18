<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$teamNumber = $_GET['teamNumber'];

$query = "SELECT d.team_number, t.team_name, AVG(f.score) as avg_score, f.bar_climb, AVG(f.tele_check) as avg_climb,
    AVG(f.tele_speed) as avg_tele_speed, SUM(f.switch_cubes + f.enemy_switch_cubes + f.scale_cubes) AS total_cubes,
    SUM(f.auto_scale +f. auto_switch) AS total_auto_cubes,
    SUM(f.vault_cubes) AS total_vault
    FROM (SELECT DISTINCT p.team_number FROM pit_comments p
UNION SELECT DISTINCT f.team_number FROM form_data f) AS d
    LEFT JOIN form_data f on f.team_number = d.team_number
    JOIN (SELECT * FROM scouters WHERE team_number = ?) s ON s.id = f.scouter_id
    LEFT JOIN team_info t ON t.team_number = d.team_number
    GROUP BY team_number";

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
