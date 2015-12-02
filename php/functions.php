<?php

function getTeamInfo($teamNumber) {
    include("../config/config.php");
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://frc-api.usfirst.org/v2.0/$tournamentYear/teams?teamNumber=$teamNumber");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        "Accept: application/json",
        "Authorization: Basic " . base64_encode($authUser . ":" . $authToken)
    ));

    $responsejson = curl_exec($ch) == false ? curl_error($ch) : json_decode(curl_exec($ch), true)["teams"][0];
    curl_close($ch);
    die(json_encode(array (
        "name" => $responsejson["nameShort"],
        "robotName" => $responsejson["robotName"]
    )));
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
	$query = "SELECT name FROM scouters WHERE name = ?";
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
    $query = "SELECT * FROM `wagers` WHERE matchPredicted = ?";
    include("../config/config.php");
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://frc-api.usfirst.org/v2.0/$tournamentYear/matches/" . $tournamentKey . "?tournamentLevel=qual&matchNumber=" . $matchNum);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        "Accept: application/json",
        "Authorization: Basic " . base64_encode($authUser . ":" . $authToken)
    ));

    $responsejson = curl_exec($ch) == false ? curl_error($ch) : json_decode(curl_exec($ch), true)["Matches"];
    curl_close($ch);
    if(!empty($responsejson["0"])) {
        $matchData = $responsejson["0"];

        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("i", $matchNum);
            $stmt->execute();
            while($row = $result->fetch_array()) {
                $byteCoinsToAdd = 0;
                switch($row["wagerType"]) {
                    case 'alliance':
                        if($matchData["scoreRedFinal"] > $matchData["scoreBlueFinal"]) {
                            if($row["alliancePredicted"] == 'red') {
                                $byteCoinsToAdd += $row["wageredByteCoins"]*2;
                            }
                        } else if ($matchData["scoreRedFinal"] > $matchData["scoreBlueFinal"]) {
                            if($row["alliancePredicted"] == 'blue') {
                                $byteCoinsToAdd += $row["wageredByteCoins"]*2;
                            }
                        } else {
                            $byteCoinsToAdd += $$row["wageredByteCoins"];
                        }
                        break;
                    case 'closeMatch':
                        if(abs($matchData["scoreRedFinal"] - $matchData["scoreBlueFinal"]) <= $row["withenPoints"]) {
                            $byteCoinsToAdd += ($row["wageredByteCoins"] / $row["withenPoints"]);
                        }
                        break;
                    case 'minPoints':
                        if($row["alliancePredicted"] == 'red') {
                            if ($matchData["scoreRedFinal"] > $row["minPointsPredicted"]) {
                                $byteCoinsToAdd += ($row["wageredByteCoins"] * round(log($row["minPointsPredicted"])) / 2);
                            }
                        } else {
                            if ($matchData["scoreBlueFinal"] > $row["minPointsPredicted"]) {
                                $byteCoinsToAdd += ($row["wageredByteCoins"] * round(log($row["minPointsPredicted"])));
                            }
                        }
                        break;
                    }
                    if($byteCoinsToAdd > 1) {
                        $query = "UPDATE scouters SET byteCoins = byteCoins + ? WHERE id = ?"; {
                            if($stmt = $db->prepare($query)) {
                                $stmt->bind_param("ii", $byteCoinsToAdd, $row["associatedId"]);
                                $stmt->execute();
                                return true;
                            }
                        }
                    }
            }
            $query = "DELETE * FROM `wagers` WHERE matchPredicted = ?";
            if($stmt = $db->prepare($query)) {
                $stmt->bind_param("i", $matchNum);
                $stmt->execute();
                return;
            }
        }
    }
    error_log("Adding Byte Coins failed");
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

function getTeamRankings($db, $team){
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
		$stmt->bind_param("ii", $team, $team);
		$stmt->execute();
		return $stmt->get_result();
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

function getTeamCoopertition($db, $team){
	$query = "SELECT match_number AS 'Match Number', coopertition_totes AS 'Co-op Totes'
				FROM scout_data
				WHERE team = ?
				ORDER BY match_number";
	if($stmt = $db->prepare($query)){
		$stmt->bind_param("i", $team);
		$stmt->execute();
		return $stmt->get_result();
	} else{
		return null;
	}
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
