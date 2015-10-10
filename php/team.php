<?php
include ("connect.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
$comments = [];
$timestamps = [];
$names = [];
$matchNumber = [];
$team = 0;
if($postData["teamNumber"]) {
    $query = 'SELECT * FROM scout_data WHERE team = ?';
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("i", $postData["teamNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $response = [];
            while ($row = $result->fetch_assoc()) {
                $team = $row["team"];
                if ($row["comments"] != "" && $row["comments"] != null) {
                    $comments[] = $row["comments"];
                    $timestamps[] = intval($row["timestamp"]);
                    $names[] = $row["name"];
                    $matchNumber[] = $row["match_number"];
                }
            }
            $response = [
                'commentSection' => [
                    'comments' => $comments,
                    'timestamps' => $timestamps,
                    'names' => $names,
                    'matchNumbers' => $matchNumber
                ],
                'teamNumber' => $team
            ];
//            echo(json_encode($response));
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
