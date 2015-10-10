<?php
    include('connect.php');
	header('Content-Type: application/json');

    $postData = json_decode(file_get_contents("php://input"), true);
    $query = "INSERT INTO pit_scouting (team_number, pit_comments, scouter_name, pic_num) VALUES (?,?,?,?)";
if($stmt = $db->prepare($query)) {
    $stmt->bind_param("issi",
                      $postData["team_number"],
                      $postData["pit_comments"],
                      $postData["scouter_name"],
                      $postData["pic_num"]);
    $stmt->execute();
    if ($stmt->error) {
        header('HTTP/1.1 500 SQL Error', true, 500);
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
	    die('{"message":"'.$stmt->error.'"}');
    }

} else {
    header('HTTP/1.1 500 SQL Error', true, 500);
    $db->close();
	die ( '{"message":"Failed creating statement"}' );
}
$db->close();
?>
