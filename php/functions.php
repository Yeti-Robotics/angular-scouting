<?php

function getLastMatch($db) {
	$query = "SELECT DISTINCT match_number FROM form_data ORDER BY match_number DESC LIMIT 1";
	$result = $db->query($query);
	if ($result) {
		$lastMatch = 0;
		if ($row = $result->fetch_array()) {
			$lastMatch = $row["match_number"];
		}
        // return $lastMatch;
        return 0;
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
}

function getFutureMatches($db) {
	//$matchResults = getMatchSchedule(); //For server
	include("../config/config.php");
    $fileName = "../json/{$eventKey}MatchResults.json";
	$matchResults = json_decode(file_get_contents($fileName), true); //For localhost
	$lastMatch = getLastMatch($db);
	$uncompletedMatches = array();
	for ($i = 0; $i < count($matchResults); $i++) {
		if ($matchResults[$i]["match_number"] >= $lastMatch) {
			$uncompletedMatches[] = $matchResults[$i];
		}
	}
	return $uncompletedMatches;
}

function getSettings() {
	$fileName = "../config/settings.json";
	return file_exists($fileName) ? json_decode(file_get_contents($fileName), true) : false;
}

function updateSettings($updatedSettings) {
	$fileName = "../config/settings.json";
	
	if (file_exists($fileName)) {
		file_put_contents($fileName, json_encode($updatedSettings, JSON_PRETTY_PRINT));
		return true;
	} else {
		return false;
	}
}

function checkPitData($db, $teamNumber, $scoutingTeam) {
	$query = "SELECT p.*, s.team_number AS scouting_team
    FROM `pit_comments` p
    LEFT JOIN scouters s ON s.id = p.scouter_id
    WHERE p.team_number = ? AND s.team_number = ?";
	$hasComments;
	$hasPics;

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("ii", $teamNumber, $scoutingTeam);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
			$hasComments = true;
        } else {
			$hasComments = false;
		}
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
	
	$query = "SELECT q.*, s.team_number AS scouting_team
    FROM `pit_pictures` q
    LEFT JOIN scouters s ON s.id = q.scouter_id
    WHERE q.team_number = ? AND s.team_number = ?";
	
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("ii", $teamNumber, $scoutingTeam);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
			$hasPics = true;
        } else {
			$hasPics = false;
		}
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
	
	return ($hasComments || $hasPics);
}

function checkTeamData($db, $teamNumber) {
	$query = "SELECT * FROM `scout_data` WHERE team_number = ?";

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
			return true;
        } else {
			return false;
		}
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        die ( '{"message":"Failed creating statement"}' );
    }
}

//returns if the match data updated or not
function updateMatchData() {
	include("../config/config.php");
    $fileName = "../json/{$eventKey}MatchResults.json";
	if (!file_exists($fileName)) {
		file_put_contents($fileName, "{}");
	}
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$TBAapiServer/event/$eventKey/matches");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        "Accept: application/json",
		"X-TBA-App-Id: $TBAAppId",
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
		file_put_contents($fileName, json_encode($responsejson, JSON_PRETTY_PRINT));
		return true;
    } else {
		return false;
	}
}

function flushSchedule() {
	include("../config/config.php");
	$fileName = "../json/{$eventKey}MatchResults.json";
	if (!file_exists($fileName)) {
		file_put_contents($fileName, "{}");
	}
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "$TBAapiServer/event/$eventKey/matches");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array (
		"Accept: application/json",
		"X-TBA-App-Id: $TBAAppId"
	));

	$responsejson = curl_exec($ch);
	curl_close($ch);
	
	file_put_contents($fileName, $responsejson);
}

function getMatchSchedule() {
	updateMatchData();
	include("../config/config.php");
    $fileName = "../json/{$eventKey}MatchResults.json";
	return json_decode(file_get_contents($fileName), true);
}

function getMatchResults($matchNumber) {
	$schedule = getMatchSchedule();
    $matchData = [];
	for ($i = 0; $i < count($schedule); $i++) {
		if ($schedule[$i]["match_number"] == $matchNumber) {
			$matchData[$schedule[$i]["comp_level"]] = $schedule[$i]["alliances"]["blue"]["score"] != -1 ? $schedule[$i] : false;
		}
	}
    return $matchData;
}

function nextMatch() {
	$schedule = getMatchSchedule();
	$matches = [];
	
	foreach($schedule as $match) {
		if($match["alliances"]["blue"]["score"] == -1) {
			$matches[] = $match["match_number"];
		}
	}
	if (count($matches) < 1) {
		return false;
	}
	$nextMatchNumber = min($matches);
	foreach ($schedule as $match) {
		if ($match["match_number"] == $nextMatchNumber) {
			return $match;
		}
	}
}

function updateTeamInfo($db, $teamNumber) {
	$ch = curl_init();
	include("../config/config.php");
	curl_setopt($ch, CURLOPT_URL, "$TBAapiServer/team/frc$teamNumber");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array (
		"Accept: application/json",
		"X-TBA-App-Id: $TBAAppId"
	));

	$responsejson = curl_exec($ch) == false ? curl_error($ch) : json_decode(curl_exec($ch), true);
	curl_close($ch);

	$robotInfo["teamNumber"] = intval($teamNumber);
	$robotInfo["name"] = isset($responsejson["nickname"]) ? $responsejson["nickname"] : null;

	if ($robotInfo["name"] != null) {
		$query = "INSERT INTO team_info (team_number, team_name) VALUES (?, ?)
	ON DUPLICATE KEY UPDATE team_name = ?";
		if($stmt = $db->prepare($query)) {
			$stmt->bind_param("iss", $robotInfo["teamNumber"], $robotInfo["name"], $robotInfo["name"]);
			$stmt->execute();
			if ($stmt->error) {
				header('HTTP/1.1 500 SQL Error', true, 500);
				$db->close();
				die('{"message":"'.$stmt->error.'"}');
			} else {
				$db->close();
			}
		}
	}
	return $robotInfo;
}

function getTeamInfo($db, $teamNumber) {
    $robotInfo = array(
        "teamNumber" => 0,
        "name" => ""
    );

    $query = "SELECT * FROM team_info WHERE team_number = ?";
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $teamNumber);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
			$robotInfo = updateTeamInfo($db, $teamNumber);
        } else {
            $query = "SELECT * FROM team_info WHERE team_number = ?";
            if($stmt = $db->prepare($query)) {
                $stmt->bind_param("i", $teamNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_array()) {
                    $robotInfo["teamNumber"] = intval($teamNumber);
                    $robotInfo["name"] = $row["team_name"];
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

function deleteToken($db, $token, $username) {
	$query = "DELETE FROM sessions WHERE token = ?";

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("s", $token);
		$stmt->execute();
		if ($stmt->error) {
			header('HTTP/1.1 500 SQL Error', true, 500);
			die ( '{"message":"Failed creating statement"}' );
		}
	} else {
		header('HTTP/1.1 500 SQL Error', true, 500);
		die ( '{"message":"Failed creating statement"}' );
	}
}

function getSessionUser($db, $token) {
    $query = "SELECT name, sessions.id AS id, username, byteCoins FROM sessions LEFT JOIN scouters ON sessions.id = scouters.id WHERE token = ?";
    if (validateToken($db, $token)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                return [
					"name" => $row["name"],
					"username" => $row["username"],
					"byteCoins" => $row["byteCoins"],
                    "id" => $row["id"]
				];
            }
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            die ( '{"message":"Failed creating getSessionUser statement"}' );
        }
    } else {
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
            $userId = getUserId($db, $username, $pswdHash);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $query = "UPDATE sessions SET token = ? WHERE id = ?";
                if($stmt = $db->prepare($query)) {
                    $stmt->bind_param("si", $token, $userId);
                    $stmt->execute();
                    return $token;
                } else {
                    header('HTTP/1.1 500 SQL Error', true, 500);
                    die ( '{"message":"Failed creating statement"}' );
                }
            } else {
                $query = "INSERT INTO sessions (id, token) VALUES (?, ?)";
                if($stmt = $db->prepare($query)) {
                    $stmt->bind_param("is", $userId, $token);
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

function checkForUserFromId($db, $id) {
	$query = "SELECT id FROM scouters WHERE id = ?";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $id);
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
	updateMatchData();
    $query = "SELECT * FROM `wagers` WHERE matchPredicted <= ?";
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $matchNum);
		$stmt->execute();
		$result = $stmt->get_result();
		while($row = $result->fetch_array()) {
			$matchData = getMatchResults($row["matchPredicted"]);
			if($matchData) {
				$byteCoinsToAdd = 0;
				switch($row["wagerType"]) {
					case 'alliance':
						if(($matchData["scoreRedFinal"] > $matchData["scoreBlueFinal"]) && $row["alliancePredicted"] == 'red') {
							$byteCoinsToAdd += $row["wageredByteCoins"]*2;
						} else if(($matchData["scoreBlueFinal"] > $matchData["scoreRedFinal"]) && $row["alliancePredicted"] == 'blue') {
							$byteCoinsToAdd += $row["wageredByteCoins"]*2;
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
				$query = "DELETE FROM `wagers` WHERE matchPredicted <= ?";
				if($stmt = $db->prepare($query)) {
					$stmt->bind_param("i", $matchNum);
					$stmt->execute();
				}
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
        die('{"error": "Failed to get data"}');
    }
}

function getTeamMainMatchTable($db, $team) {
    $query = "SELECT match_number, auto_gear, autoHighGoal, autoHighAccuracy, autoShootSpeed, autoLowGoal, autoLowAccuracy, teleHighGoal, teleHighAccuracy, teleShootSpeed, teleLowGoal, teleLowAccuracy, teleGears
	FROM `scout_data`
	WHERE team_number = ?";
    $return = array();
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $team);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $return["auto"][] = array(
					"match_number" => $row["match_number"],
					"auto_gear" => $row["auto_gear"],
					"autoHighGoal" => $row["autoHighGoal"],
					"autoHighAccuracy" => $row["autoHighAccuracy"],
					"autoShootSpeed" => $row["autoShootSpeed"],
					"autoLowGoal" => $row["autoLowGoal"],
					"autoLowAccuracy" => $row["autoLowAccuracy"]
				);
				$return["teleop"][] = array(
					"match_number" => $row["match_number"],
					"teleGears" => $row["teleGears"],
					"teleHighGoal" => $row["teleHighGoal"],
					"teleHighAccuracy" => $row["teleHighAccuracy"],
					"teleShootSpeed" => $row["teleShootSpeed"],
					"teleLowGoal" => $row["teleLowGoal"],
					"teleLowAccuracy" => $row["teleLowAccuracy"]
				);
            }
        }
        return $return;
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . '500 SQL Error', true, 500);
        die('{"error": "Failed to get data"}');
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
            if ($autoCases['crosses_defense'] > $autoCases['crosses_two_defenses']) {
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
        foreach ($match as $key => $defenses){
            $crosscount += $key != "match_number" ? $defenses : 0;
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
            if ($autoCases['crosses_defense'] > $autoCases['crosses_two_defenses']) {
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

	WHERE team_number = ?
	GROUP BY team";
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

function getPitComments($db, $team, $scoutingTeam) {
	$query = "SELECT p.team_number AS 'Team', 
    p.pit_comments AS 'Pit Scouters Comments', 
    s.name AS 'Pit Scouter', 
    UNIX_TIMESTAMP(p.timestamp) AS timestamp,
    s.team_number AS scouting_team
    FROM pit_comments p
    LEFT JOIN scouters s ON s.id = p.scouter_id
    WHERE p.team_number = ? AND p.pit_comments != '' AND s.team_number = ?";
	//Time stamps?
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $team, $scoutingTeam);
		$stmt->execute();
		return $stmt->get_result();
	}
	else {
		return null;
	}
}

function getPicInfo($db, $team, $scoutingTeam) {
    $dir = scandir("../pics/$team");
    array_splice($dir, 0, 2);
    for ($i = 0; $i < count($dir); $i++) {
        $dir[$i] = intval(substr($dir[$i], 0, -4));
    }
	$query = "SELECT p.team_number AS 'Team', 
    s.name AS 'Pit Scouter', 
    p.pic_num AS 'Picture Number', 
    UNIX_TIMESTAMP(p.timestamp) AS timestamp,
    s.team_number AS scouting_team
    FROM pit_pictures p
    LEFT JOIN scouters s ON s.id = p.scouter_id
    WHERE p.team_number = ? AND s.team_number =?";
    for ($i = 0; $i < count($dir); $i++) {
        $query .= " " . ($i == 0 ? "AND (" : "OR ") . "pic_num = $dir[$i]" . ($i == (count($dir) - 1) ? ")" : "");
    }
	//Time stamps?
	if($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $team, $scoutingTeam );
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
