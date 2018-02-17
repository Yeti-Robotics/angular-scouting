<?php
include('connect.php');
include('functions.php');
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
if(checkForUser($db, $postData["username"]) == false) {
	$query = "INSERT INTO scouters (name, username, pswd, team_number) VALUES (?, ?, ?, ?)";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("sssi",
			$postData["name"],
			$postData["username"],
			md5($postData["password"]),
			$postData["teamNumber"]);
	    $stmt->execute();
	    if ($stmt->error) {
	        header('HTTP/1.1 500 SQL Error', true, 500);
	        $db->close();
		    die('{"message":"'.$stmt->error.'"}');
	    }
	    header('HTTP/1.1 204 No content', true, 204);
	    $db->close();
	}
}
else {
	header('HTTP/1.1 403 Attempted to make existing account');
	die('{"message":"Attempted to make existing account"}');
}
?>