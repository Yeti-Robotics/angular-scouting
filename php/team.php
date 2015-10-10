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
            $response['commentSection'] = [
                'comments' => $comments,
                'timestamps' => $timestamps,
                'names' => $names,
                'matchNumbers' => $matchNumber
            ];
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to upload points");
    }
    
    $query = "SELECT t1.team AS Team, ROUND(t1.avg_height,2) AS 'avgStackHeight', ROUND(t2.avg_stacks,2) AS 'avgStacksperMatch', MAX(t4.totes) AS 'heighestStackMade', ROUND(rating,2) AS 'rating'
FROM (SELECT team, AVG(totes) AS avg_height, totes
FROM stacks
LEFT JOIN scout_data ON scout_data.scout_data_id=stacks.scout_data_id
GROUP BY team) AS t1
LEFT JOIN (SELECT team, COUNT(totes > 0) / COUNT(DISTINCT match_number) AS avg_stacks
FROM stacks
RIGHT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
GROUP BY team
ORDER BY team DESC) AS t2 ON t1.team = t2.team
LEFT JOIN (SELECT AVG(rating) AS rating, team
					FROM scout_data
					GROUP BY team) AS t3 ON t1.team=t3.team
                    
LEFT JOIN (SELECT team, totes
				FROM stacks
				LEFT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
				WHERE totes > 0 AND team = ?
			    GROUP BY totes, cap_height, match_number
				ORDER BY match_number, totes) AS t4 ON t4.team=t1.team
WHERE t1.team=?";
     if($stmt = $db->prepare($query)){
        $stmt->bind_param("ii", $postData["teamNumber"], $postData["teamNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            if ($row = $result->fetch_assoc()) {
                $response['teamSection'] = [
                    'teamNumber' => $row['Team'],
                    'avgStackHeight' => $row['avgStackHeight'],
                    'avgStacksperMatch' => $row['avgStacksperMatch'],
                    'heighestStackMade' => $row['heighestStackMade'],
                    'rating' => $row['rating']
                ];
            }
        }
     } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to upload points");
     }
     
     $query = "SELECT match_number AS 'matchNumber', totes AS 'stackHeight', cap_height AS 'capHeight', COUNT(totes) AS 'numberOfStacks'
				FROM stacks
				LEFT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
				WHERE totes > 0 AND team = ?
			    GROUP BY totes, cap_height, match_number
				ORDER BY match_number, totes";
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("i", $postData["teamNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['stacksSection'][] = [
                    'matchNumber' => $row['matchNumber'],
                    'stackHeight' => $row['stackHeight'],
                    'capHeight' => $row['capHeight'],
                    'numberOfStacks' => $row['numberOfStacks']
                ];
            }
        }
     } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to upload points");
     }
    
    $query = "SELECT match_number AS 'matchNumber', if(totes_from_landfill, 'yes', 'no') AS 'totesLandfill', if(totes_from_human, 'yes', 'no') AS 'totesHumanPlayer'
				FROM `scout_data`
				WHERE team=?";
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("i", $postData["teamNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['toteSupplySection'][] = [
                    'matchNumber' => $row['matchNumber'],
                    'totesLandfill' => $row['totesLandfill'],
                    'totesHumanPlayer' => $row['totesHumanPlayer']
                ];
            }
        }
     } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to upload points");
     }
    
     $query = "SELECT match_number AS 'matchNumber', coopertition_totes AS 'co-opTotes'
				FROM scout_data
				WHERE team = ?
				ORDER BY match_number";
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("i", $postData["teamNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['coopSection'][] = [
                    'matchNumber' => $row['matchNumber'],
                    'coopTotes' => $row['co-opTotes']
                ];
            }
        }
     } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to upload points");
     }

die(json_encode($response));
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die("Failed to upload points");
}
?>