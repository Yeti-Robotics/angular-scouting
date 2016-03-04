<?php

//returns if the match data updated or not
function updateMatchData() {
	include("../config/config.php");
    $fileName = "../json/" . $tournamentKey . "MatchResults.json";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$apiServer/$tournamentYear/schedule/$tournamentKey/qual/hybrid");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        "Accept: application/json",
        "Authorization: Basic " . base64_encode($authUser . ":" . $authToken),
        "If-Modified-Since: " . date(DATE_RSS, file_exists($fileName) ? filemtime($fileName) : time())
    ));

    $responsejson = curl_exec($ch);
	curl_close($ch);
    $headerText = substr($responsejson, 0, strpos($responsejson, "\r\n\r\n"));
    $headers = array();
    foreach (explode("\r\n", $headerText) as $i => $line) {
        if ($i == 0) {
            $headers["Status-Code"] = substr($line, strpos($line, " ") + 1);
        } else {
            list ($key, $value) = explode(": ", $line);
            $headers[$key] = $value;
        }
    }

    $responsejson = json_decode(trim(substr($responsejson, strpos($responsejson, "\r\n\r\n"))), true);
    if (!strpos($headers["Status-Code"], "304") && $responsejson != null) {
        $file = fopen($fileName, "w");
        fwrite($file, json_encode($responsejson));
        fclose($file);
		return true;
    } else {
		return false;
	}
}

function getMatchSchedule() {
	updateMatchData();
	include("../config/config.php");
    $fileName = "../json/" . $tournamentKey . "MatchResults.json";
	return json_decode(file_get_contents($fileName), true)["Schedule"];
}

function getMatchResults($matchNumber) {
    $matchData = getMatchSchedule()[$matchNumber - 1];
    return $matchData["scoreRedFinal"] != null ? $matchData : false;
}

function nextMatch() {
	$schedule = getMatchSchedule();
	foreach($schedule as $match) {
		if(empty($match["actualStartTime"])) {
			return $match["matchNumber"];
		}
	}
}

function updateTeamInfo($db, $teamNumber) {
	$ch = curl_init();
	include("../config/config.php");
	curl_setopt($ch, CURLOPT_URL, "$apiServer/$tournamentYear/teams?teamNumber=$teamNumber");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array (
		"Accept: application/json",
		"Authorization: Basic " . base64_encode($authUser . ":" . $authToken)
	));

	$responsejson = curl_exec($ch) == false ? curl_error($ch) : json_decode(curl_exec($ch), true)["teams"][0];
	curl_close($ch);

	$robotInfo["teamNumber"] = intval($teamNumber);
	$robotInfo["name"] = $responsejson["nameShort"] != null ? $responsejson["nameShort"] : "N/A";
	$robotInfo["robotName"] = $responsejson["robotName"] != null ? $responsejson["robotName"] : "N/A";

	$query = "INSERT INTO team_info (team_number, team_name, robot_name) VALUES (?, ?, ?)";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("iss", $teamNumber, $robotInfo["name"], $robotInfo["robotName"]);
		$stmt->execute();
		if ($stmt->error) {
			header('HTTP/1.1 500 SQL Error', true, 500);
			$db->close();
			die('{"message":"'.$stmt->error.'"}');
		}
	}
	return $robotInfo;
}

function getTeamInfo($db, $teamNumber) {
    $robotInfo = array(
        "teamNumber" => 0,
        "name" => "",
        "robotName" => ""
    );

    $query = "SELECT * FROM team_info WHERE team_number = ?";
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows == 0) {
			//$robotInfo = updateTeamInfo($db, $teamNumber);
        } else {
            $query = "SELECT * FROM team_info WHERE team_number = ?";
            if($stmt = $db->prepare($query)) {
                $stmt->bind_param("i", $teamNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_array()) {
                    $robotInfo["teamNumber"] = intval($teamNumber);
                    $robotInfo["name"] = $row["team_name"];
                    $robotInfo["robotName"] = $row["robot_name"];
                }
            } else {
                header('HTTP/1.1 500 SQL Error', true, 500);
                die ( '{"message":"Failed creating statement"}' );
            }
        }
        return $robotInfo;
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
}

function validateToken($db, $token) {
    $query = "SELECT * FROM sessions WHERE token = ?";
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
}

function getSessionUser($db, $token) {
    $query = "SELECT name FROM sessions LEFT JOIN scouters ON sessions.id = scouters.id WHERE token = ?";
    if (validateToken($db, $token)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                return $row[0];
            }
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating statement"}' );
        }
    }
    else {
        return false;
    }
}

function isUserAdmin($db, $token) {
    $query = "SELECT * FROM sessions LEFT JOIN scouters ON sessions.id = scouters.id WHERE token = ?";
    if (validateToken($db, $token)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
				include("../config/config.php");
				return ($row[5] == $adminUsername);
            }
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating statement"}' );
        }
    }
    else {
        return false;
    }
}

function startSession($db, $username, $pswdHash) {
    $query = "SELECT * FROM sessions WHERE id = ?";
    $token = ($pswdHash . md5(time()));
    if (checkPassword($db, $username, $pswdHash)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("i", getUserId($db, $username, $pswdHash));
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $query = "UPDATE sessions SET token = ? WHERE id = ?";
                if($stmt = $db->prepare($query)) {
                    $stmt->bind_param("si", $token, getUserId($db, $username, $pswdHash));
                    $stmt->execute();
                    return $token;
                } else {
                    header('HTTP/1.1 500 SQL Error', true, 500);
                    die ( '{"message":"Failed creating statement"}' );
                }
            } else {
                $query = "INSERT INTO sessions (id, token) VALUES (?, ?)";
                if($stmt = $db->prepare($query)) {
                    $stmt->bind_param("is", getUserId($db, $username, $pswdHash), $token);
                    $stmt->execute();
                    return $token;
                } else {
                    header('HTTP/1.1 500 SQL Error', true, 500);
                    die ( '{"message":"Failed creating statement"}' );
                }
            }
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating statement"}' );
        }
    } else {
        return false;
    }
    
    $query = "INSERT INTO sessions (id, token) VALUES (?, ?)";
    $token = ($pswdHash . md5(time()));
    if (checkPassword($db, $username, $pswdHash)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("is", getUserId($db, $username, $pswdHash), $token);
            $stmt->execute();
            return $token;
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating statement"}' );
        }
    }
    else {
        return false;
    }
}

function getUserId($db, $username, $pswdHash) {
    $query = "SELECT id FROM `scouters` WHERE username = ?";
    if (checkPassword($db, $username, $pswdHash)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                return $row[0];
            }
        }
    }
    else {
        return false;
    }
}

function getUserIdFromToken($db, $token) {
	$query = "SELECT id FROM sessions WHERE token = ?";
	if(validateToken($db, $token)) {
		if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                return $row[0];
            }
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating statement"}' );
        }
	} else {
        return false;
    }
}

function checkPassword($db, $username, $pswdHash) {
    $query = "SELECT pswd FROM `scouters` WHERE username = ?";

    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_array()) {
            if($row[0] == $pswdHash) {
                return true;
            }
        }
        return false;
    }
}

function getName($db, $username, $pswdHash) {
    $query = "SELECT name FROM `scouters` WHERE username = ?";
    if (checkPassword($db, $username, $pswdHash)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                return $row[0];
            }
        }
    }
    else {
        return false;
    }
}

function checkForUser($db, $username) {
	$query = "SELECT username FROM scouters WHERE username = ?";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("s", $username);
        	$stmt->execute();
        	$stmt->store_result();
        	if($stmt->num_rows > 0) {
        		return true;
        	} else {
        		return false;
        	}
	}
}

function updateQualificationWagers($db, $matchNum) {
    $query = "SELECT * FROM `wagers` WHERE matchPredicted <= ?";
	$matchData = getMatchResults($matchNum);
	if($matchData) {
		if($stmt = $db->prepare($query)) {
			$stmt->bind_param("i", $matchNum);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_array()) {
				$byteCoinsToAdd = 0;
				switch($row["wagerType"]) {
					case 'alliance':
						if($matchData["scoreRedFinal"] > $matchData["scoreBlueFinal"]) {
							if($row["alliancePredicted"] == 'red') {
								$byteCoinsToAdd += $row["wageredByteCoins"]*2;
							}
						} else if($matchData["scoreRedFinal"] > $matchData["scoreBlueFinal"]) {
							if($row["alliancePredicted"] == 'blue') {
								$byteCoinsToAdd += $row["wageredByteCoins"]*2;
							}
						} else {
							$byteCoinsToAdd += $row["wageredByteCoins"];
						}
						break;
					case 'closeMatch':
						if(abs($matchData["scoreRedFinal"] - $matchData["scoreBlueFinal"]) <= $row["withenPoints"]) {
							$byteCoinsToAdd += $row["wageredByteCoins"] * (5 - ($row["withenPoints"] / 12.5));
						}
						break;
					case 'minPoints':
						if($row["alliancePredicted"] == 'red') {
							if($matchData["scoreRedFinal"] >= $row["withenPoints"]) {
								$byteCoinsToAdd += $row["wageredByteCoins"] * round(($row["withenPoints"] / 110) + ($row["withenPoints"] / 350));
							}
						} else {
							if($matchData["scoreBlueFinal"] >= $row["withenPoints"]) {
								$byteCoinsToAdd += $row["wageredByteCoins"] * round(($row["withenPoints"] / 110) + ($row["withenPoints"] / 350));
							}
						}
						break;
				}
				if($byteCoinsToAdd > 1) {
					$query = "UPDATE scouters SET byteCoins = byteCoins + ? WHERE id = ?";
					if($stmt = $db->prepare($query)) {
						$stmt->bind_param("ii", $byteCoinsToAdd, $row["associatedId"]);
						$stmt->execute();
					}
				}
			}
			$query = "DELETE FROM `wagers` WHERE matchPredicted <= ?";
			if($stmt = $db->prepare($query)) {
				$stmt->bind_param("i", $matchNum);
				$stmt->execute();
			}
		}
	}
}

function getByteCoins($db, $token) {
	if($id = getUserIdFromToken($db, $token)) {
		$query = "SELECT byteCoins FROM scouters WHERE id = ?";
		if($stmt = $db->prepare($query)) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_array()) {
				die(json_encode($row[0]));
			}
		}
		else {
			die("{'message' : 'SQL Error'} ");
		}
	} else {
		return false;
	}
}

function getTeamStacksTable($db, $team){
	$query = "SELECT match_number AS 'Match Number', totes AS 'Stack Height', cap_height AS 'Cap Height', COUNT(totes) AS 'Number of Stacks'
				FROM stacks
				LEFT JOIN scout_data ON scout_data.scout_data_id = stacks.scout_data_id
				WHERE totes > 0 AND team = ?
			    GROUP BY totes, cap_height, match_number
				ORDER BY match_number, totes";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	} else{
		return null;
	}
}

function getTeamAutoTable($db, $team){
	$query = "SELECT match_number AS 'Match Number', if(robot_moved, 'yes', 'no') AS 'Robot Moved', totes_auto AS 'Number of totes moved', cans_auto AS 'Number of cans moved', if(cans_auto_origin, 'step', 'auto zone') AS 'Where did cans come from?', if(in_auto_zone, 'yes', 'no') AS 'Finishes in Auto Zone?'
				FROM scout_data
				WHERE team=?";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	} else{
		return null;
	}
}

function getTeamDefenseTable($db, $team){
   $query = "SELECT match_number, robot_moved, gametime, low_bar, portcullis,
	cheval_de_frise, moat, ramparts, drawbridge,
	sally_port, rock_wall, rough_terrain FROM scout_data
	LEFT JOIN defenses
	ON scout_data.scout_data_id = defenses.id
	WHERE scout_data.team=?";
	$return = array();
    if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $team);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
				if($row["gametime"] == "auto") {
					$return["auto"][] = array(
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
					$return["teleop"][] = array(
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
        return $return;
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to get data");
    }
}

function getTeamBouldersTable($db, $team) {
    $query = "SELECT match_number, auto_balls_high, auto_balls_low, 
                teleop_balls_high, teleop_balls_low
                FROM scout_data WHERE team=?";
    $return = array();
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $team);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $return["auto"][] = array(
					"match_number" => $row["match_number"],
					"balls_scored_low" => $row["auto_balls_low"],
					"balls_scored_high" => $row["auto_balls_high"]
				);
				$return["teleop"][] = array(
					"match_number" => $row["match_number"],
					"balls_scored_low" => $row["teleop_balls_low"],
					"balls_scored_high" => $row["teleop_balls_high"]
				);
            }
        }
        return $return;
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die("Failed to get data");
    }
}

function getTeamAutoString($db, $team){
    $defenses = getTeamDefenseTable($db, $team);
    $balls = getTeamBouldersTable($db, $team); 
	$return = array();
    $autoCases = array(
        'doesnt_drive' => 0,
        'reaches_defense' => 0,
        'crosses_defense' =>0,
        'crosses_two_defenses' => 0,
        'doesnt_score' => 0,
        'scores_low' => 0,
        'scores_two_low' => 0,
        'scores_three_low' => 0,
        'scores_high' => 0,
        'scores_two_high' => 0,
        'scores_three_high' => 0
    );
    foreach ($defenses['auto'] as $match){
        $crosscount = 0;
        foreach ($match as $defenses){
            $crosscount += $defenses; 
        }
        switch ($crosscount) {
        case 0:
            $autoCases['doesnt_drive']++;
            break;
        case 1:
            $autoCases['reaches_defense']++;
            break;
        case 2:
            $autoCases['crosses_defense']++;
            break;
        case 3:
            $autoCases['crosses_two_defenses']++;
            break;
        } 
    }
    

    foreach ($balls['auto'] as $match){
        switch ($match['balls_scored_low'] ) {
        case 1:
            $autoCases['scores_low']++;
            break;
        case 2:
            $autoCases['scores_two_low']++;
            break;
        case 3:
            $autoCases['scores_three_low']++;
            break;
        default:
            break;
        }
        switch ($match['balls_scored_high'] ) {
        case 1:
            $autoCases['scores_high']++;
            break;
        case 2:
            $autoCases['scores_two_high']++;
            break;
        case 3:
            $autoCases['scores_three_high']++;
            break;
        default:
            break;
        }
        if ($match['balls_scored_low'] == 0 && $match['balls_scored_high'] == 0) {
            $autoCases['doesnt_score']++;
        }
    }
    //dd, rd, cd, c2d
    if ($autoCases['doesnt_drive'] > $autoCases['reaches_defense']) {
        // dd, cd, c2d
        if ($autoCases['doesnt_drive'] > $autoCases['crosses_defense']) {
            //dd, c2d
            if ($autoCases['doesnt_drive'] > $autoCases['crosses_two_defenses']) {
                //dd
                $return['auto_common_defense'] = "Doesn't drive";
            } else {
                //c2d
                $return['auto_common_defense'] = "Crosses two defenses";
            }
        } else {
            //cd, c2d
            if ($autoCases.['crosses_defense'] > $autoCases['crosses_two_defenses']) {
                //cd
                $return['auto_common_defense'] = "Crosses a defense";
            } else {
                //c2d
                $return['auto_common_defense'] = "Crosses two defenses";
            }
        }
    } else if ($autoCases['reaches_defense'] > $autoCases['crosses_defense']) {
        //rd, c2d
        if ($autoCases['reaches_defense'] > $autoCases['crosses_two_defenses']) {
            //rd
            $return['auto_common_defense'] = "Drives to a defense";
        } else {
            //c2d
            $return['auto_common_defense'] = "Crosses two defenses";
        }
    } else if ($autoCases['crosses_defense'] > $autoCases['crosses_two_defenses']) {
        //cd
        $return['auto_common_defense'] = "Crosses a defense";
    } else {
        //c2d
        $return['auto_common_defense'] = "Crosses two defenses";
    }

    // 0, 1l, 2l, 3l, 1h, 2h, 3h 
    /*
    doesnt_score: 0,
    scores_low: 0,
    scores_two_low: 0,
    scores_three_low: 0,
    scores_high: 0,
    scores_two_high: 0,
    scores_three_high: 0
    */

    if ($autoCases['doesnt_score'] > $autoCases['scores_low']) {
        // 0, 2l, 3l
        if ($autocases['doesnt_score'] > $autoCases['scores_two_low']) {
            // 0, 3l
            if ($autoCases['doesnt_score'] > $autoCases['scores_three_low']) {
                //0
                $return['auto_common_scoring']['low']= "Doesn't Score Low";

            } else {
                $return['auto_common_scoring']['low'] = "Scores Three Low";

            }
        } else {
            if ($autoCases['scores_two_low'] > $autoCases['scores_three_low']) {
                $return['auto_common_scoring']['low'] = "Scores Two Low";
            } else {
                $return['auto_common_scoring']['low'] = "Scores Three Low";
            }
        }
    } else if ($autoCases['scores_low'] > $autoCases['scores_two_low']) {
        if ($autoCases['scores_low'] > $autoCases['scores_three_low']) {
            $return['auto_common_scoring']['low'] = "Scores Low";
        } else {
            $return['auto_common_scoring']['low'] = "Scores Three Low ";

        }
    } else if ($autoCases['scoring_two_low'] > $autoCases['scores_three_low']) {
        $return['auto_common_scoring']['low'] = "Scores Two Low";

    } else {
        $return['auto_common_scoring']['low'] = "scores Three Low";
    }

    //Now we do the high 
    if ($autoCases['doesnt_score'] > $autoCases['scores_high']) {
        // 0, 2h, 3h
        if ($autocases['doesnt_score'] > $autoCases['scores_two_high']) {
            // 0, 3h
            if ($autoCases['doesnt_score'] > $autoCases['scores_three_high']) {
                //0
                $return['auto_common_scoring']['high'] = "Doesn't Score High";

            } else {
                $return['auto_common_scoring']['high'] = "Scores Three High";

            }
        } else {
            if ($autoCases['scores_two_high'] > $autoCases['scores_three_high']) {
                $return['auo_common_scoring']['high'] = "Scores Two High";

            } else {
                $return['auto_common_scoring']['High'] = "Scores Three High";
            }
        }
    } else if ($autoCases['scores_high'] > $autoCases['scores_two_high']) {
        if ($autoCases['scores_high'] > $autoCases['scores_three_high']) {
            $return['auto_common_scoring']['high'] = "Scores High";
        } else {
            $return['auto_common_scoring']['high'] = "Scores Three High";

        }
    } else if ($autoCases['scoring_two_high'] > $autoCases['scores_three_high']) {
        $return['auto_common_scoring']['high'] = "Scores Two High";

    } else {
        $return['auto_common_scoring']['high'] = "scores Three High";
    }

	return $return;
}

function getTeamAutoStringWTables($db, $team, $defenses, $balls){
	$return = array();
    $autoCases = array(
        'doesnt_drive' => 0,
        'reaches_defense' => 0,
        'crosses_defense' =>0,
        'crosses_two_defenses' => 0,
        'doesnt_score' => 0,
        'scores_low' => 0,
        'scores_two_low' => 0,
        'scores_three_low' => 0,
        'scores_high' => 0,
        'scores_two_high' => 0,
        'scores_three_high' => 0
    );
    foreach ($defenses['auto'] as $match){
        $crosscount = 0;
        foreach ($match as $defenses){
            $crosscount += $defenses;
        }
        switch ($crosscount) {
        case 0:
            $autoCases['doesnt_drive']++;
            break;
        case 1:
            $autoCases['reaches_defense']++;
            break;
        case 2:
            $autoCases['crosses_defense']++;
            break;
        case 3:
            $autoCases['crosses_two_defenses']++;
            break;
        }
    }


    foreach ($balls['auto'] as $match){
        switch ($match['balls_scored_low'] ) {
        case 1:
            $autoCases['scores_low']++;
            break;
        case 2:
            $autoCases['scores_two_low']++;
            break;
        case 3:
            $autoCases['scores_three_low']++;
            break;
        default:
            break;
        }
        switch ($match['balls_scored_high'] ) {
        case 1:
            $autoCases['scores_high']++;
            break;
        case 2:
            $autoCases['scores_two_high']++;
            break;
        case 3:
            $autoCases['scores_three_high']++;
            break;
        default:
            break;
        }
        if ($match['balls_scored_low'] == 0 && $match['balls_scored_high'] == 0) {
            $autoCases['doesnt_score']++;
        }
    }
    //dd, rd, cd, c2d
    if ($autoCases['doesnt_drive'] > $autoCases['reaches_defense']) {
        // dd, cd, c2d
        if ($autoCases['doesnt_drive'] > $autoCases['crosses_defense']) {
            //dd, c2d
            if ($autoCases['doesnt_drive'] > $autoCases['crosses_two_defenses']) {
                //dd
                $return['auto_common_defense'] = "Doesn't drive";
            } else {
                //c2d
                $return['auto_common_defense'] = "Crosses two defenses";
            }
        } else {
            //cd, c2d
            if ($autoCases.['crosses_defense'] > $autoCases['crosses_two_defenses']) {
                //cd
                $return['auto_common_defense'] = "Crosses a defense";
            } else {
                //c2d
                $return['auto_common_defense'] = "Crosses two defenses";
            }
        }
    } else if ($autoCases['reaches_defense'] > $autoCases['crosses_defense']) {
        //rd, c2d
        if ($autoCases['reaches_defense'] > $autoCases['crosses_two_defenses']) {
            //rd
            $return['auto_common_defense'] = "Drives to a defense";
        } else {
            //c2d
            $return['auto_common_defense'] = "Crosses two defenses";
        }
    } else if ($autoCases['crosses_defense'] > $autoCases['crosses_two_defenses']) {
        //cd
        $return['auto_common_defense'] = "Crosses a defense";
    } else {
        //c2d
        $return['auto_common_defense'] = "Crosses two defenses";
    }

    // 0, 1l, 2l, 3l, 1h, 2h, 3h
    /*
    doesnt_score: 0,
    scores_low: 0,
    scores_two_low: 0,
    scores_three_low: 0,
    scores_high: 0,
    scores_two_high: 0,
    scores_three_high: 0
    */

    if ($autoCases['doesnt_score'] > $autoCases['scores_low']) {
        // 0, 2l, 3l
        if ($autoCases['doesnt_score'] > $autoCases['scores_two_low']) {
            // 0, 3l
            if ($autoCases['doesnt_score'] > $autoCases['scores_three_low']) {
                //0
                $return['auto_common_scoring']['low']= "Doesn't Score Low";

            } else {
                $return['auto_common_scoring']['low'] = "Scores Three Low";

            }
        } else {
            if ($autoCases['scores_two_low'] > $autoCases['scores_three_low']) {
                $return['auto_common_scoring']['low'] = "Scores Two Low";
            } else {
                $return['auto_common_scoring']['low'] = "Scores Three Low";
            }
        }
    } else if ($autoCases['scores_low'] > $autoCases['scores_two_low']) {
        if ($autoCases['scores_low'] > $autoCases['scores_three_low']) {
            $return['auto_common_scoring']['low'] = "Scores Low";
        } else {
            $return['auto_common_scoring']['low'] = "Scores Three Low ";

        }
    } else if ($autoCases['scores_two_low'] > $autoCases['scores_three_low']) {
        $return['auto_common_scoring']['low'] = "Scores Two Low";

    } else {
        $return['auto_common_scoring']['low'] = "Scores Three Low";
    }

    //Now we do the high
    if ($autoCases['doesnt_score'] > $autoCases['scores_high']) {
        // 0, 2h, 3h
        if ($autoCases['doesnt_score'] > $autoCases['scores_two_high']) {
            // 0, 3h
            if ($autoCases['doesnt_score'] > $autoCases['scores_three_high']) {
                //0
                $return['auto_common_scoring']['high'] = "Doesn't Score High";

            } else {
                $return['auto_common_scoring']['high'] = "Scores Three High";

            }
        } else {
            if ($autoCases['scores_two_high'] > $autoCases['scores_three_high']) {
                $return['auo_common_scoring']['high'] = "Scores Two High";

            } else {
                $return['auto_common_scoring']['High'] = "Scores Three High";
            }
        }
    } else if ($autoCases['scores_high'] > $autoCases['scores_two_high']) {
        if ($autoCases['scores_high'] > $autoCases['scores_three_high']) {
            $return['auto_common_scoring']['high'] = "Scores High";
        } else {
            $return['auto_common_scoring']['high'] = "Scores Three High";

        }
    } else if ($autoCases['scores_two_high'] > $autoCases['scores_three_high']) {
        $return['auto_common_scoring']['high'] = "Scores Two High";

    } else {
        $return['auto_common_scoring']['high'] = "Scores Three High";
    }

	return $return;
}

function getTeamRankings($db, $team){
	$query = "SELECT totalLowBars.*, gcd.gcdName, totalHighGoals.totalHighGoals, totalLowGoals.totalLowGoals, gamesDefended.gamesDefended
FROM (SELECT team, SUM(low_bar) AS totalLowBars
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
LEFT JOIN (SELECT team, CONCAT(ROUND((SUM(robot_defended) / COUNT(match_number)) * 100), '%') AS gamesDefended
FROM scout_data
 GROUP BY team) AS gamesDefended ON totalLowBars.team = gamesDefended.team WHERE totalLowBars.team = ?";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result()->fetch_assoc();
	} else{
		return null;
	}
}

function getTeamTotesOriginTable($db, $team){
	$query = "SELECT match_number AS 'Match Number', if(totes_from_landfill, 'yes', 'no') AS 'Totes Landfill?', if(totes_from_human, 'yes', 'no') AS 'Totes Human Player?'
				FROM `scout_data`
				WHERE team=?";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	} else{
		return null;
	}
}

function timeAgo($timestamp){
	$difference = time() - $timestamp;
	$periods = array("second", "minute", "hour", "day", "week", "month", "years", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	for($j = 0; $difference >= $lengths[$j]; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	$text = "$difference $periods[$j] ago";
	return $text;
}

function makeImageHTML($imgCode) {
	return "<img src='" . $imgCode . "' alt='php did not work'>";
}

function makeDir($team, $pic) {
	return "pics/" . $team . "/" . $pic . ".txt";
}

function getPic($team, $pic) {
	$file = file(makeDir($team, $pic));
	return $file[0];
}

function getPitComments($db, $team) {
	$query = "SELECT team_number AS 'Team', pit_comments AS 'Pit Scouters Comments', scouter_name AS 'Pit Scouter', UNIX_TIMESTAMP(timestamp) AS timestamp
				FROM pit_comments
				WHERE team_number = ? AND pit_comments != ''";
	//Time stamps?
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	}
	else {
		return null;
	}
}

function getPicInfo($db, $team) {
    $dir = scandir("../pics/$team");
    array_splice($dir, 0, 2);
    for ($i = 0; $i < count($dir); $i++) {
        $dir[$i] = intval(substr($dir[$i], 0, -4));
    }
	$query = "SELECT team_number AS 'Team', scouter_name AS 'Pit Scouter', pic_num AS 'Picture Number', UNIX_TIMESTAMP(timestamp) AS timestamp
				FROM pit_pictures
				WHERE team_number = ?";
    for ($i = 0; $i < count($dir); $i++) {
        $query .= " " . ($i == 0 ? "AND (" : "OR ") . "pic_num = $dir[$i]" . ($i == (count($dir) - 1) ? ")" : "");
    }
	//Time stamps?
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	}
	else {
		return null;
	}
}

function resizeImage($src, $dst) {

	header ( 'Content-Type: image/jpeg' );

	list ( $width, $height ) = getimagesize ( $src );

	$newwidth = $width;
	$newheight = $height;

	if ($width > 640 || $height > 640) {
		$ratio = 640 / max ( [
				$width,
				$height
		] );
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
	}

	$newImg = imagecreatetruecolor ( $newwidth, $newheight );
	$source = imagecreatefromjpeg( $src );

	imagecopyresized ( $newImg, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

	imagejpeg ( $newImg, $dst );
}
?>
