<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');
$query = "SELECT team_number AS team, avgScoreTable.avgScore AS avgScore, gearTable.totalGears AS totalGears, autoHighAcc.accName AS autoHighAcc, teleHighAcc.accName AS teleHighAcc, autoLowAcc.accName AS autoLowAcc, teleLowAcc.accName AS teleLowAcc, climbTable.avgClimbed AS avgClimbed
FROM scout_data

LEFT JOIN (SELECT team_number AS team, ROUND(AVG(score)) AS avgScore
FROM `scout_data`
GROUP BY team_number) as avgScoreTable ON avgScoreTable.team = scout_data.team_number

LEFT JOIN (SELECT team_number AS team, SUM(auto_gear) + SUM(teleGears) AS totalGears
FROM `scout_data`
GROUP BY team_number) as gearTable ON gearTable.team = scout_data.team_number

LEFT JOIN (SELECT team_number AS team, ROUND(AVG(climbed) * 100) AS avgClimbed
FROM `scout_data`
GROUP BY team_number) AS climbTable ON climbTable.team = scout_data.team_number

LEFT JOIN (SELECT t1.team, CASE t1.autoHighAccuracy
WHEN 0 THEN '0% (No Accuracy)'
WHEN 1 THEN '~30% (Low Accuracy)'
WHEN 2 THEN '~50% (Medium Accuracy)'
WHEN 3 THEN '~80% (High Accuracy)'
END AS accName
FROM (SELECT team_number AS team, autoHighAccuracy, COUNT(*) AS autoHighAccCount
FROM scout_data
GROUP BY autoHighAccuracy, team_number) AS t1
JOIN (SELECT t1.team AS team, MAX(t1.autoHighAccCount) AS maxAutoHighAccCount
FROM (SELECT team_number AS team, autoHighAccuracy, COUNT(*) AS autoHighAccCount
FROM scout_data
GROUP BY autoHighAccuracy, team_number) AS t1
GROUP BY t1.team) AS t2 ON t1.team = t2.team AND t1.autoHighAccCount = t2.maxAutoHighAccCount
GROUP BY t1.team
ORDER BY t1.team DESC) AS autoHighAcc ON autoHighAcc.team = scout_data.team_number

LEFT JOIN (SELECT t1.team, CASE t1.teleHighAccuracy
WHEN 0 THEN '0% (No Accuracy)'
WHEN 1 THEN '~30% (Low Accuracy)'
WHEN 2 THEN '~50% (Medium Accuracy)'
WHEN 3 THEN '~80% (High Accuracy)'
END AS accName
FROM (SELECT team_number AS team, teleHighAccuracy, COUNT(*) AS teleHighAccCount
FROM scout_data
GROUP BY teleHighAccuracy, team_number) AS t1
JOIN (SELECT t1.team AS team, MAX(t1.teleHighAccCount) AS maxAutoLowAccCount
FROM (SELECT team_number AS team, teleHighAccuracy, COUNT(*) AS teleHighAccCount
FROM scout_data
GROUP BY teleHighAccuracy, team_number) AS t1
GROUP BY t1.team) AS t2 ON t1.team = t2.team AND t1.teleHighAccCount = t2.maxAutoLowAccCount
GROUP BY t1.team
ORDER BY t1.team DESC) AS teleHighAcc ON teleHighAcc.team = scout_data.team_number

LEFT JOIN (SELECT t1.team, CASE t1.autoLowAccuracy
WHEN 0 THEN '0% (No Accuracy)'
WHEN 1 THEN '~30% (Low Accuracy)'
WHEN 2 THEN '~50% (Medium Accuracy)'
WHEN 3 THEN '~80% (High Accuracy)'
END AS accName
FROM (SELECT team_number AS team, autoLowAccuracy, COUNT(*) AS autoLowAccCount
FROM scout_data
GROUP BY autoLowAccuracy, team_number) AS t1
JOIN (SELECT t1.team AS team, MAX(t1.autoLowAccCount) AS maxAutoLowAccCount
FROM (SELECT team_number AS team, autoLowAccuracy, COUNT(*) AS autoLowAccCount
FROM scout_data
GROUP BY autoLowAccuracy, team_number) AS t1
GROUP BY t1.team) AS t2 ON t1.team = t2.team AND t1.autoLowAccCount = t2.maxAutoLowAccCount
GROUP BY t1.team
ORDER BY t1.team DESC) AS autoLowAcc ON autoLowAcc.team = scout_data.team_number

LEFT JOIN (SELECT t1.team, CASE t1.teleLowAccuracy
WHEN 0 THEN '0% (No Accuracy)'
WHEN 1 THEN '~30% (Low Accuracy)'
WHEN 2 THEN '~50% (Medium Accuracy)'
WHEN 3 THEN '~80% (High Accuracy)'
END AS accName
FROM (SELECT team_number AS team, teleLowAccuracy, COUNT(*) AS teleLowAccCount
FROM scout_data
GROUP BY teleLowAccuracy, team_number) AS t1
JOIN (SELECT t1.team AS team, MAX(t1.teleLowAccCount) AS maxAutoLowAccCount
FROM (SELECT team_number AS team, teleLowAccuracy, COUNT(*) AS teleLowAccCount
FROM scout_data
GROUP BY teleLowAccuracy, team_number) AS t1
GROUP BY t1.team) AS t2 ON t1.team = t2.team AND t1.teleLowAccCount = t2.maxAutoLowAccCount
GROUP BY t1.team
ORDER BY t1.team DESC) AS teleLowAcc ON teleLowAcc.team = scout_data.team_number

GROUP BY team";

$result = $db->query ( $query );
if ($result) {
    $output = array();
    
    while ($row = $result->fetch_assoc()) {
    	$row['avgScore'] = intval($row['avgScore']);
    	$row['totalGears'] = intval($row['totalGears']);
    	$row['autoHighAcc'] = $row['autoHighAcc'];
    	$row['teleHighAcc'] = $row['teleHighAcc'];
    	$row['autoLowAcc'] = $row['autoLowAcc'];
    	$row['teleLowAcc'] = $row['teleLowAcc'];
    	$row['avgClimbed'] = intval($row['avgClimbed']);
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
