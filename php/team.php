<?php
include ("connect.php");
header('Content-Type: application/json');
$postData = json_decode(file_get_contents("php://input"), true);
if($postData["teamNumber"]) {
    $response = array();
    $query = "SELECT t1.team AS Team, ROUND(t1.avg_height,2) AS 'Avg. Stack Height', ROUND(t2.avg_stacks,2) AS 'Avg. Stacks per Match', MAX(t4.totes) AS 'Highest Stack Made', ROUND(rating,2) AS 'Rating'
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
            die(json_encode($result));
            $response["team"] = array();
            while ($row = $result->fetch_assoc()) {
                $team = $row["team"];
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
