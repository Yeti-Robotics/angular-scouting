<?php
include('connect.php');
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$query = "INSERT INTO scouters (name, pswd) VALUES (?, ?)";
if($stmt = $db->prepare($query)) {
    $stmt->bind_param("ss", $postData["username"], md5($postData["password"]));
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }
    header('HTTP/1.1 204 No content', true, 204);
    $db->close();
}
?>