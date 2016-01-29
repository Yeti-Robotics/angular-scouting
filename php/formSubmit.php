<?php
include("connect.php");
include("functions.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO scout_data (name, match_number,
         team, robot_moved, auto_balls_crossed, auto_balls_high,
         auto_balls_low, teleop_balls_high, teleop_balls_low, robot_defended, rating, score, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

if($stmt = $db->prepare($query)) {
    $stmt->bind_param("siiiiiiiiiiis",
        $postData["name"],
        $postData["match_number"],
		$postData["team"],
        $robot_moved,
        $postData["auto_balls_crossed"],
        $postData["auto_balls_high"],
        $postData["auto_balls_low"],
        $postData["teleop_balls_high"],
        $postData["teleop_balls_low"],
        $robot_defended,
        $postData["rating"],
		$postData["score"],
        $postData["comments"],
        $in_auto_zone);
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

updateTeamInfo($db, $postData["team"]);

$db->close();
?>
