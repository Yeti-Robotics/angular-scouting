<?php
include("connect.php");
include("functions.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO form_data (
			auto_check,
			auto_switch,
			auto_scale,
			bar_climb,
			comment,
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
			scouter_id,
			vault_cubes,
			tele_cube_stack)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$autoCheck = isset($postData['autoCheck']) ? intval($postData['autoCheck']) : '';
$autoSwitch = isset($postData['autoSwitch']) ? intval($postData['autoSwitch']) : '';
$autoScale = isset($postData['autoScale']) ? intval($postData['autoScale']) : '';
$barClimb = isset($postData['barClimb']) ? intval($postData['barClimb']) : '';
$comment = isset($postData['comment']) ? strip_tags($postData['comment']) : '';
$enemySwitchCubes = isset($postData['enemySwitchCubes']) ? $postData['enemySwitchCubes'] : '';
$helpClimb = isset($postData['helpClimb']) ? intval($postData['helpClimb']) : '';
$matchNumber = isset($postData['matchNumber']) ? $postData['matchNumber'] : '';
$otherClimb = isset($postData['otherClimb']) ? strip_tags($postData['otherClimb']) : '';
$rampClimb = isset($postData['rampClimb']) ? intval($postData['rampClimb']) : '';
$scaleCubes = isset($postData['scaleCubes']) ? $postData['scaleCubes'] : '';
$score = isset($postData['score']) ? $postData['score'] : '';
$switchCubes = isset($postData['switchCubes']) ? $postData['switchCubes'] : '';
$teamNumber = isset($postData['teamNumber']) ? $postData['teamNumber'] : '';
$teleCheck = isset($postData['teleCheck']) ? intval($postData['teleCheck']) : '';
$teleDefense = isset($postData['teleDefense']) ? intval($postData['teleDefense']) : '';
$scouterId = isset($postData['scouterId']) ? intval($postData['scouterId']) : '';
$vaultCubes = isset($postData['vaultCubes']) ? intval($postData['vaultCubes']) : '';
$teleCubeStack = isset($postData['teleCubeStack']) ? intval($postData['teleCubeStack']) : '';

if($stmt = $db->prepare($query)) {
	$stmt->bind_param("iiiisiiisiiiiiiiiii",
		$autoCheck,
		$autoSwitch,
		$autoScale,
		$barClimb,
		$comment,
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
		$scouterId,
		$vaultCubes,
		$teleCubeStack
	);
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }
    if (!checkTeamData($db, $teamNumber)) {
        updateTeamInfo($db, $teamNumber);
    }
	$insert_id = $stmt->insert_id;
} else {
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
	die ( "{'message':'Failed creating statement'}" );
}

$db->close();
?>
