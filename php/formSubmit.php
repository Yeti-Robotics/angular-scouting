<?php
include("connect.php");
include("functions.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO form_data (
			auto_check,
			auto_defend,
			auto_scale,
			auto_speed,
			bar_climb,
			comment,
			cube_ranking,
			enemy_switch_cubes,
			help_climb,
			match_number,
			other_climb,
			ramp_climb,
			scale_cubes,
			score,
			switch_cubes,
			team_number,
			tele_check,
			tele_defense,
			tele_speed)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$autoCheck = isset($postData['autoCheck']) ? intval($postData['autoCheck']) : null;
$autoDefend = isset($postData['autoDefend']) ? intval($postData['autoDefend']) : null;
$autoScale = isset($postData['autoScale']) ? intval($postData['autoScale']) : null;
$autoSpeed = isset($postData['autoSpeed']) ? $postData['autoSpeed'] : null;
$barClimb = isset($postData['barClimb']) ? intval($postData['barClimb']) : null;
$comment = isset($postData['comment']) ? strip_tags($postData['comment']) : null;
$cubeRanking = isset($postData['cubeRanking']) ? $postData['cubeRanking'] : null;
$enemySwitchCubes = isset($postData['enemySwitchCubes']) ? $postData['enemySwitchCubes'] : null;
$helpClimb = isset($postData['helpClimb']) ? intval($postData['helpClimb']) : null;
$matchNumber = isset($postData['matchNumber']) ? $postData['matchNumber'] : null;
$otherClimb = isset($postData['otherClimb']) ? strip_tags($postData['otherClimb']) : null;
$rampClimb = isset($postData['rampClimb']) ? intval($postData['rampClimb']) : null;
$scaleCubes = isset($postData['scaleCubes']) ? $postData['scaleCubes'] : null;
$score = isset($postData['score']) ? $postData['score'] : null;
$switchCubes = isset($postData['switchCubes']) ? $postData['switchCubes'] : null;
$teamNumber = isset($postData['teamNumber']) ? $postData['teamNumber'] : null;
$teleCheck = isset($postData['teleCheck']) ? intval($postData['teleCheck']) : null;
$teleDefense = isset($postData['teleDefense']) ? intval($postData['teleDefense']) : null;
$teleSpeed = isset($postData['teleSpeed']) ? $postData['teleSpeed'] : null;

if($stmt = $db->prepare($query)) {
	$stmt->bind_param("iiiiisiiiisiiiiiiii",
		$autoCheck,
		$autoDefend,
		$autoScale,
		$autoSpeed,
		$barClimb,
		$comment,
		$cubeRanking,
		$enemySwitchCubes,
		$helpClimb,
		$matchNumber,
		$otherClimb,
		$rampClimb,
		$scaleCubes,
		$score,
		$switchCubes,
		$teamNumber,
		$teleCheck,
		$teleDefense,
		$teleSpeed
	);
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }
	$insert_id = $stmt->insert_id;
} else {
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
	die ( '{"message":"Failed creating statement"}' );
}

$db->close();
?>
