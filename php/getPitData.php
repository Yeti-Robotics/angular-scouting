<?php
include('connect.php');
include('functions.php');
header('Content-Type: application/json');

$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : 0;

if ($teamNumber) {
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
    die(json_encode($response));
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die("Failed to upload points");
}
?>