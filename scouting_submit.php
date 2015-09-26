<?php
include ("connect.php");

$query = "INSERT INTO scout_data (team, match_number,
		 robot_moved, totes_auto, cans_auto, coopertition,
		 coopertition_totes, score, comments, rating, name, 
		cans_from_middle, totes_from_landfill, totes_from_human, cans_auto_origin, in_auto_zone) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

//  Insert team record
if($stmt = $db->prepare($query)){
	$robot_moved = 0;
	if(isset($_POST["robot_moved"])){
		$robot_moved = 1;
	} else{
		$robot_moved = 0;
	}
	$cans_from_middle = 0;
	if(isset($_POST["cans_from_middle"])){
		$cans_from_middle = 1;
	} else{
		$cans_from_middle = 0;
	}
	$totes_from_landfill = 0;
	if(isset($_POST["totes_from_landfill"])){
		$totes_from_landfill = 1;
	} else{
		$totes_from_landfill = 0;
	}
	$totes_from_human = 0;
	if(isset($_POST["totes_from_human"])){
		$totes_from_human = 1;
	} else{
		$totes_from_human = 0;
	}
	$coopertition_number = 0;
	if(isset($_POST["coopertition_number"])){
		$coopertition_number = 1;
	} else{
		$coopertition_number = 0;
	}
	$in_auto_zone = 0;
	if (isset($_POST["in_auto_zone"])) {
		$in_auto_zone = 1;
	} else {
		$in_auto_zone = 0;
	}
	$stmt->bind_param("iiiiiiiisisiiiii", 
		$_POST["team_number"],
		$_POST["match_number"],
		$robot_moved,
		$_POST["totes_auto"],
		$_POST["cans_auto"],
		$coopertition_number,
		$_POST["coopertition_totes"],
		$_POST["score"],
		$_POST["comments"],
		$_POST["rating"],
		$_POST["name"],
		$cans_from_middle,
		$totes_from_landfill,
		$totes_from_human,
		$_POST["cans_auto_origin"],
		$in_auto_zone);
	
	$stmt->execute();
	$insert_id = $stmt->insert_id;
	
	//  Insert stack records
	if (isset($_POST["stacks_totes"]) && isset($_POST["capped_stack"])) {
		foreach ($_POST["stacks_totes"] as $index => $totes) {
			$stack_query = "INSERT INTO stacks (scout_data_id, totes, cap_state, cap_height)
					VALUES (?, ?, ?, ?)";
			if ($stack_stmt = $db->prepare($stack_query)) {
				$stack_stmt->bind_param("iiii", $insert_id, $totes, $_POST["capped_stack"][$index], $_POST["cap_height"][$index]);
				$stack_stmt->execute();
			}
		}
	}
	
	if ($insert_id > 0) {
		header("Location: http://" . $_SERVER['HTTP_HOST']);
	} else {
		header("HTTP/1.1 418");
		echo "<h1>Upload failed.</h1>";
	}
}
$db->close();
?>
<h2 class="link" onclick="history.back()">Back</h2>

