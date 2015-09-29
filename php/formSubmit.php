<?php
include("connect.php");

$query = "INSERT INTO scout_data (team, match_number,
		 robot_moved, totes_auto, cans_auto, coopertition_totes, score, comments, rating, name, cans_from_middle, totes_from_landfill, totes_from_human, cans_auto_origin, in_auto_zone) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$postData = json_decode(file_get_contents("php://input"), true);
if($stmt = $db->prepare($query)){
    $stmt->bind_param("iiiiiiisisiiiii", 
		$postData["team_number"],
		$postData["match_number"],
		$postData["robot_moved"],
		$postData["totes_auto"],
		$postData["cans_auto"],
		$postData["coopertition_totes"],
		$postData["score"],
		$postData["comments"],
		$postData["rating"],
		$postData["name"],
		$postData["cans_from_middle"],
		$postData["totes_from_landfill"],
		$postData["totes_from_human"],
		$postData["cans_auto_origin"],
		$postData["in_auto_zone"]);
	
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