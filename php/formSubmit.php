<?php
include("connect.php");
include("functions.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO scout_data (id,
			match_number,
			team_number,
            robot_moved,
            auto_gear,
			autoHighGoal,
            autoHighAccuracy,
            autoShootSpeed,
            autoLowGoal,
            autoLowAccuracy,
			teleHighGoal,
			teleHighAccuracy,
            teleShootSpeed,
			teleLowGoal,
			teleLowAccuracy,
			teleGears,
            `load`,
            climbed,
			score,
			comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if($postData["robot_moved"]) {
	$robot_moved = 1;
} else {
	$robot_moved = 0;
}
if($postData["auto_gear"]) {
	$auto_gear = 1;
} else {
	$auto_gear = 0;
}
if($postData["autoHighGoal"]) {
	$autoHighGoal = 1;
} else {
	$autoHighGoal = 0;
}
if($postData["autoLowGoal"]) {
	$autoLowGoal = 1;
} else {
	$autoLowGoal = 0;
}
if($postData["teleHighGoal"]) {
	$teleHighGoal = 1;
} else {
	$teleHighGoal = 0;
}
if($postData["teleLowGoal"]) {
	$teleLowGoal = 1;
} else {
	$teleLowGoal = 0;
}
if($postData["climbed"]) {
	$climbed = 1;
} else {
	$climbed = 0;
}

if($stmt = $db->prepare($query)) {
    $stmt->bind_param("iiiiiiiiiiiiiiiiiiis",
        $postData["id"],
        $postData["match_number"],
		$postData["team_number"],
        $robot_moved,
        $auto_gear,
		$autoHighGoal,
		$postData["autoHighAccuracy"],
        $postData["autoShootSpeed"],
		$autoLowGoal,
        $postData["autoLowAccuracy"],
		$teleHighGoal,
        $postData["teleHighAccuracy"],
        $postData["teleShootSpeed"],
		$teleLowGoal,
        $postData["teleLowAccuracy"],
		$postData["teleGears"],
		$postData["load"],
        $climbed,
		$postData["score"],
        strip_tags($postData["comments"]));
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }
    $insert_id = $stmt->insert_id;

	updateQualificationWagers($db, $postData["match_number"]);
	updateTeamInfo($db, $postData["team_number"]);
} else {
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
	die ( '{"message":"Failed creating statement"}' );
}

$db->close();
?>
