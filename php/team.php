<?php
include ("connect.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
if($postData["teamNumber"]) {
    $query = 'SELECT * FROM scout_data WHERE team = ?';
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("i", $_GET["team"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $response = array();
            while ($row = $result->fetch_assoc()) {
                $team = $row["team"];
                if ($row["comments"] != "" && $row["comments"] != null) {
                    $comments[] = $row["comments"];
                    $timestamps[] = intval($row["timestamp"]);
                    $names[] = $row["name"];
                    $matchNumber[] = $row["match_number"];
                }
            }
        die(json_encode($response));
        }
    }
    header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
    die("Failed to upload points");

} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die("Failed to upload points");
}
?>
