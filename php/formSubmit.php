<?php
include("connect.php");

$query = "INSERT INTO scout_data (team, match_number,
		 robot_moved, totes_auto, cans_auto, coopertition,
		 coopertition_totes, score, comments, rating, name,
		cans_from_middle, totes_from_landfill, totes_from_human, cans_auto_origin, in_auto_zone)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$postData = json_decode(file_get_contents("php://input"), true);
if($stmt = $db->prepare($query)){
    $robot_moved = 0;
	if(isset($postData["robot_moved"])){
		$robot_moved = 1;
	} else{
		$robot_moved = 0;
	}
	$cans_from_middle = 0;
	if(isset($postData["cans_from_middle"])){
		$cans_from_middle = 1;
	} else{
		$cans_from_middle = 0;
	}
	$totes_from_landfill = 0;
	if(isset($postData["totes_from_landfill"])){
		$totes_from_landfill = 1;
	} else{
		$totes_from_landfill = 0;
	}
	$totes_from_human = 0;
	if(isset($postData["totes_from_human"])){
		$totes_from_human = 1;
	} else{
		$totes_from_human = 0;
	}
	$coopertition_number = 0;
	if(isset($postData["coopertition_number"])){
		$coopertition_number = 1;
	} else{
		$coopertition_number = 0;
	}
	$in_auto_zone = 0;
	if (isset($postData["in_auto_zone"])) {
		$in_auto_zone = 1;
	} else {
		$in_auto_zone = 0;
	}

    echo(json_encode($postData));

    $stmt->bind_param("iiiiiiiisisiiiii",
		$postData["team_number"],
		$postData["match_number"],
		$robot_moved,
		$postData["totes_auto"],
		$postData["cans_auto"],
		$coopertition_number,
		$postData["coopertition_totes"],
		$postData["score"],
		$postData["comments"],
		$postData["rating"],
		$postData["name"],
		$cans_from_middle,
		$totes_from_landfill,
		$totes_from_human,
		$postData["cans_auto_origin"],
		$in_auto_zone);

	$stmt->execute();
	$insert_id = $stmt->insert_id;

    if(count($postData["stackRows"]["rows"]) > 0) {
        foreach ($postData["stackRows"]["rows"] as $stack) {
            $stack_query = "INSERT INTO stacks (scout_data_id, totes, cap_state, cap_height)
					VALUES (?, ?, ?, ?)";
            if ($stack_stmt = $db->prepare($stack_query)) {
				$stack_stmt->bind_param("iiii", $insert_id, $stack["stacks_totes"], $stack["capped_stack"], $stack["cap_height"]);
				$stack_stmt->execute();
                echo("Stack height: ".$stack["stacks_totes"].", Cap state: ".$stack["capped_stack"].", Cap height: ".$stack["cap_height"]."\n\n");
			}
        }
    }
}
$db->close();
?>
