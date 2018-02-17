<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$query = "SELECT *
	FROM team_info
	WHERE team_number IN (SELECT DISTINCT team_number FROM scouters)
	ORDER BY CASE team_number WHEN 3506 THEN 0 ELSE team_number END";

$output = array();
$result = $db->query($query);
if ($result) {
	while($row = $result->fetch_assoc()) {
		$output[] = $row;
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
	die('{"error": "Failed to retrive team data, problem with query 1"}');
}

die(json_encode($output));

$db->close ();

?>
