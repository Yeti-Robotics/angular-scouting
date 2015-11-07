<?php
include ("connect.php");
header('Content-Type: application/json');
$query = "SELECT t1.team AS 'team', ROUND(t1.avg_height,2) AS 'avgStackHeight', ROUND(t2.avg_stacks,2) AS 'avgStacksPerMatch', IFNULL(MAX(max_totes), 0) AS 'highestStackMade', ROUND(rating,2) AS 'rating'
FROM (SELECT team, AVG(totes) AS avg_height, totes
FROM scout_data
LEFT JOIN stacks ON scout_data.scout_data_id=stacks.scout_data_id
GROUP BY team) AS t1
LEFT JOIN (SELECT team, COUNT(totes > 0) / COUNT(DISTINCT match_number) AS avg_stacks
FROM stacks
RIGHT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
GROUP BY team
ORDER BY team DESC) AS t2 ON t1.team = t2.team
LEFT JOIN (SELECT AVG(rating) AS rating, team
					FROM scout_data
					GROUP BY team) AS t3 ON t1.team=t3.team
                    
LEFT JOIN (SELECT team, totes AS max_totes
				FROM stacks
				LEFT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
				WHERE totes > 0
			    GROUP BY totes, cap_height, match_number
				ORDER BY match_number, totes) AS t4 ON t4.team=t1.team
GROUP BY team";
$result = $db->query ( $query );
if ($result) {
    $output = array();
    
    while ($row = $result->fetch_assoc()) {
        $output[] = $row;
    }
        
    echo(json_encode($output));
    
} else {
	$db->close ();
    header('HTTP/1.1 500 SQL Error', true, 500);
	die ( '{"error":"wasone"}' );
}

$db->close ();

?>