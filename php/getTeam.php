<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');

$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : -1;
$response = array();
if ($teamNumber) {
	$query = "SELECT * FROM form_data WHERE team_number = ?";
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while($row = $result->fetch_assoc()) {
				$response['formData'][] = $row;
			}
			echo(json_encode($response));
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
		die('{"error": "Failed to retrive team data, problem with query"}');
	}
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die('{"error": "Failed to retrive team data"}');
}
?>
