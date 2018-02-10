<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');
$query = "
SELECT f.team_number, t.team_name, AVG(f.score) as avg_score, f.bar_climb
FROM form_data f
LEFT JOIN team_info t ON t.team_number = f.team_number
GROUP BY f.team_number
";

$result = $db->query ( $query );
if ($result) {
    $output = array();
    
    while ($row = $result->fetch_assoc()) {
    	// $row['avgScore'] = intval($row['avgScore']);
    	// $row['totalGears'] = intval($row['totalGears']);
    	// $row['autoHighAcc'] = $row['autoHighAcc'];
    	// $row['teleHighAcc'] = $row['teleHighAcc'];
    	// $row['autoLowAcc'] = $row['autoLowAcc'];
    	// $row['teleLowAcc'] = $row['teleLowAcc'];
    	// $row['avgClimbed'] = intval($row['avgClimbed']);
    	// $row['team'] = intval($row['team']);
		// $row['name'] = getTeamInfo($db, intval($row['team']))['name'];
        $output[] = $row;
    }
        
} else {
	$db->close ();
    header('HTTP/1.1 500 SQL Error', true, 500);
	die ( '{"error":"wasone"}' );
}

die(json_encode($output));

$db->close ();

?>
