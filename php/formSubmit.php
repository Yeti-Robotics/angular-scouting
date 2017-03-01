<?php
include("connect.php");
include("functions.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO scout_data (name, match_number,
         team, robot_moved, auto_gear, auto_balls_crossed, auto_balls_high,
         auto_balls_low, teleop_balls_high, teleop_balls_low,
		 robot_defended, climbed, rating, score, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if($postData["robot_moved"]) {
	$robot_moved = 1;
} else {
	$robot_moved = 0;
}
if($postData["robot_defended"]) {
	$robot_defended = 1;
} else {
	$robot_defended = 0;
}
if($postData["climbed"]) {
	$climbed = 1;
} else {
	$climbed = 0;
}
if($postData["auto_gear"]) {
	$auto_gear = 1;
} else {
	$auto_gear = 0;
}
if($stmt = $db->prepare($query)) {
    $stmt->bind_param("siiiiiiiiiiiis",
        $postData["name"],
        $postData["match_number"],
		$postData["team_number"],
        $robot_moved,
        $auto_gear,
        $postData["auto_balls_crossed"],
        $postData["auto_balls_high"],
        $postData["auto_balls_low"],
        $postData["teleop_balls_high"],
        $postData["teleop_balls_low"],
        $robot_defended,
        $climbed,
        $postData["rating"],
		$postData["score"],
        strip_tags($postData["comments"]));
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }
    $insert_id = $stmt->insert_id;

	/*$defense_query = "INSERT INTO `defenses` (`id`, `gametime`, `low_bar`, `portcullis`, `cheval_de_frise`, `moat`, `ramparts`, `drawbridge`, `sally_port`, `rock_wall`, `rough_terrain`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
	if($defense_stmt = $db->prepare($defense_query)) {
		$auto = "auto";
		$defense_stmt->bind_param("isiiiiiiiii", $insert_id,
			$auto,
			$postData["auto_defense_crossed"]["low_bar"],
			$postData["auto_defense_crossed"]["portcullis"],
			$postData["auto_defense_crossed"]["cheval_de_frise"],
			$postData["auto_defense_crossed"]["moat"],
			$postData["auto_defense_crossed"]["ramparts"],
			$postData["auto_defense_crossed"]["drawbridge"],
			$postData["auto_defense_crossed"]["sally_port"],
			$postData["auto_defense_crossed"]["rock_wall"],
			$postData["auto_defense_crossed"]["rough_terrain"]);
		$defense_stmt->execute();
	} else {
		header('HTTP/1.1 500 SQL Error', true, 500);
		$db->close();
		die ( '{"message":"Failed creating defenses statement"}' );
	}
*/
	if($defense_stmt = $db->prepare($defense_query)) {
		$teleop = "teleop";
		$defense_stmt->bind_param("isiiiiiiiii", $insert_id,
			$teleop,
			$postData["teleop_defense_crossed"]["low_bar"],
			$postData["teleop_defense_crossed"]["portcullis"],
			$postData["teleop_defense_crossed"]["cheval_de_frise"],
			$postData["teleop_defense_crossed"]["moat"],
			$postData["teleop_defense_crossed"]["ramparts"],
			$postData["teleop_defense_crossed"]["drawbridge"],
			$postData["teleop_defense_crossed"]["sally_port"],
			$postData["teleop_defense_crossed"]["rock_wall"],
			$postData["teleop_defense_crossed"]["rough_terrain"]);
		$defense_stmt->execute();
	} else {
		header('HTTP/1.1 500 SQL Error', true, 500);
		$db->close();
		die ( '{"message":"Failed creating defenses statement"}' );
	}
	updateQualificationWagers($db, $postData["match_number"]);
	updateTeamInfo($db, $postData["team_number"]);
} else {
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
	die ( '{"message":"Failed creating statement"}' );
}

$db->close();
?>
