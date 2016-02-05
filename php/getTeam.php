<?php
include ("connect.php");
include ("functions.php");
header('Content-Type: application/json');
$teamNumber = isset($_GET["teamNumber"]) ? $_GET["teamNumber"] : -1;
$comments = array();
$timestamps = array();
$names = array();
$matchNumber = array();
$team = 0;
if($teamNumber) {
    $response = array();

	//Comments query
    $query = "SELECT team, comments, UNIX_TIMESTAMP(timestamp) AS timestamp, name, match_number
			FROM scout_data
			WHERE team = ?";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
           // $response["team"] = array();
            while ($row = $result->fetch_assoc()) {
                //print_r($row);
                $row['timestamp'] = timeAgo($row['timestamp']);
                $response['commentSection'][] = $row;
            }
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to get comments");
    }

	//Boulders and endgame query
	$query = "SELECT match_number, auto_balls_high,
        auto_balls_low, teleop_balls_high, teleop_balls_low, end_game
		FROM scout_data WHERE team=?";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
				$response["balls"]["auto"][] = array(
					"match_number" => $row["match_number"],
					"balls_scored_low" => $row["auto_balls_low"],
					"balls_scored_high" => $row["auto_balls_high"]
				);
				$response["balls"]["teleop"][] = array(
					"match_number" => $row["match_number"],
					"balls_scored_low" => $row["teleop_balls_low"],
					"balls_scored_high" => $row["teleop_balls_high"]
				);

				$response["endgame"][] = array(
					"match_number" => $row["match_number"],
					"end_game" => $row["end_game"]
				);
			}
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to get data");
    }

	//Defenses query
	$query = "SELECT match_number, robot_moved, gametime, low_bar, portcullis,
	cheval_de_frise, moat, ramparts, drawbridge,
	sally_port, rock_wall, rough_terrain FROM scout_data
	LEFT JOIN defenses
	ON scout_data.scout_data_id = defenses.id
	WHERE scout_data.team=?";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
				if($row["gametime"] == "auto") {
					$response["defenses"]["auto"][] = array(
						"match_number" => $row["match_number"],
						"robot_moved" => $row["robot_moved"],
						"low_bar" => $row["low_bar"],
						"portcullis" => $row["portcullis"],
						"cheval_de_frise" => $row["cheval_de_frise"],
						"moat" => $row["moat"],
						"ramparts" => $row["ramparts"],
						"drawbridge" => $row["drawbridge"],
						"sally_port" => $row["sally_port"],
						"rock_wall" => $row["rock_wall"],
						"rough_terrain" => $row["rough_terrain"]);
				} else {
					$response["defenses"]["teleop"][] = array(
						"match_number" => $row["match_number"],
						"robot_moved" => $row["robot_moved"],
						"low_bar" => $row["low_bar"],
						"portcullis" => $row["portcullis"],
						"cheval_de_frise" => $row["cheval_de_frise"],
						"moat" => $row["moat"],
						"ramparts" => $row["ramparts"],
						"drawbridge" => $row["drawbridge"],
						"sally_port" => $row["sally_port"],
						"rock_wall" => $row["rock_wall"],
						"rough_terrain" => $row["rough_terrain"]);
				}
			}
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to get data");
    }

    $response['teamInfo'] = getTeamInfo($db, $teamNumber);
	echo(json_encode($response));
} else {
    header($_SERVER['SERVER_PROTOCOL'] . '403 No headers', true, 403);
    die("Failed to upload points");
}
?>
