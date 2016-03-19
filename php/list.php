<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');
$query = "SELECT DISTINCT totalLowBars.*, teamName.team_name AS name, gcd.gcdName, totalHighGoals.totalHighGoals, totalLowGoals.totalLowGoals, gamesDefended.gamesDefended
FROM (SELECT team, SUM(low_bar) AS totalLowBars, AVG(rating) as averageRating, (SUM(low_bar) + SUM(portcullis) + SUM(cheval_de_frise) + SUM(moat) + SUM(ramparts) + SUM(drawbridge) + SUM(sally_port) + SUM(rock_wall) + SUM(rough_terrain)) as totalDefenses
FROM defenses
LEFT JOIN scout_data ON defenses.id = scout_data.scout_data_id
GROUP BY team) AS totalLowBars
LEFT JOIN (SELECT team, CASE GREATEST(SUM(low_bar), SUM(portcullis), SUM(cheval_de_frise), SUM(moat), SUM(ramparts), SUM(drawbridge), SUM(sally_port), SUM(rock_wall), SUM(rough_terrain))
	WHEN SUM(low_bar) THEN 'Low bar'
	WHEN SUM(portcullis) THEN 'Portcullis'
	WHEN SUM(cheval_de_frise) THEN 'Cheval de frise'
	WHEN SUM(moat) THEN 'Moat'
	WHEN SUM(ramparts) THEN 'Ramparts'
	WHEN SUM(drawbridge) THEN 'Drawbridge'
	WHEN SUM(sally_port) THEN 'Sally port'
	WHEN SUM(rock_wall) THEN 'Rock wall'
	WHEN SUM(rough_terrain) THEN 'Rough terrain'
END AS gcdName
FROM defenses
LEFT JOIN scout_data ON defenses.id = scout_data.scout_data_id
GROUP BY team) AS gcd ON totalLowBars.team = gcd.team
LEFT JOIN (SELECT team, ROUND(SUM(teleop_balls_high) + SUM(auto_balls_high)) AS totalHighGoals
FROM scout_data
GROUP BY team) AS totalHighGoals ON totalLowBars.team = totalHighGoals.team
LEFT JOIN (SELECT team, ROUND(SUM(teleop_balls_low) + SUM(auto_balls_low)) AS totalLowGoals
FROM scout_data
GROUP BY team) AS totalLowGoals ON totalLowBars.team = totalLowGoals.team
LEFT JOIN (SELECT team, ROUND((SUM(robot_defended) / COUNT(match_number)) * 100) AS gamesDefended
FROM scout_data
 GROUP BY team) AS gamesDefended ON totalLowBars.team = gamesDefended.team
 LEFT JOIN (SELECT team_number, team_name FROM team_info) AS teamName ON totalLowBars.team = teamName.team_number";
$result = $db->query ( $query );
if ($result) {
    $output = array();
    
    while ($row = $result->fetch_assoc()) {
    	$row['totalDefenses'] = intval($row['totalDefenses']);
    	$row['totalLowGoals'] = intval($row['totalLowGoals']);
    	$row['totalHighGoals'] = intval($row['totalHighGoals']);
    	$row['totalLowBars'] = intval($row['totalLowBars']);
    	$row['averageRating'] = floatval($row['averageRating']);
    	$row['gamesDefended'] = intval($row['gamesDefended']);
    	$row['team'] = intval($row['team']);
		$row['name'] = getTeamInfo($db, intval($row['team']))['name'];
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
