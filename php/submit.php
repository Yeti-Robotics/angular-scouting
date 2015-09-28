<?php
include("connect.php");

$query = "INSERT INTO scout_data (team, match_number,
		 robot_moved, totes_auto, cans_auto, coopertition,
		 coopertition_totes, score, comments, rating, name, 
		cans_from_middle, totes_from_landfill, totes_from_human, cans_auto_origin, in_auto_zone) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$postDataJson = file_get_contents("php://input");
$postDataObject = json_decode($postDataJson);
$postData = [];
foreach ($postDataObject as $key => $value) {
    $postData[$key] = $postDataObject->$key;
    echo("$key: ".$postData[$key]."\n");
}
if($stmt = $db->prepare($query)){
    $stmt->bind_param("iiiiiiiisisiiiii", 
		$postData["team_number"],
		$postData["match_number"],
		$postData["robot_moved"],
		$postData["totes_auto"],
		$postData["cans_auto"],
		$postData["coopertition_number"],
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
    
}
$db->close();
?>