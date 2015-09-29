<?php
function checkPassword($db, $id, $pswd) {
    $query = "SELECT pswd FROM `scouters` WHERE id = ?";

    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_array()) {
            if($row[0] == $pswd) {
                return true;
            }
        }
        return false;
    }
}

function getName($db, $id, $pswd) {
    $query = "SELECT name FROM `scouters` WHERE id = ?";
    if (checkPassword($db, $id, $pswd)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                $db->close();
                die($row[0]);
            }
        }
    }
    else {
        return false;
    }
}

function byteCoinsToAdd($db, $id) {
    $query = "SELECT * FROM wagers WHERE associatedId = ?";
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($results) {
            $matchs = json_decode(file_get_contents("../json/NCRE.json"));
            $byteCoinsToAdd = 0;
            
            while($row = $result->fetch_array()) {
                if(strtotime($matchs["Schedule"][$row["matchPredicted"]]["startTime"]) < strtotime("now")) {
                    $options = array("timeout"=>2);
                    $request = new HttpRequest("https://frc-api.usfirst.org/v2.0/2015/matchs/NCRE" . $row["matchPredicted"]);
                    $request->setOptions($options);
                    $request->addHeaders(array(
                                               "Accept" => "application/json",
                                               "Authorization" => base64_encode("user:token")
                                               ));
                    $request->send();
                    $matchData = json_decode($request->getResponseBody())["Matches"][0];
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
                }
            }
            return $byteCoinsToAdd;
        }
    }
    die ("Getting Byte Coins to add failed");
}

function addByteCoins($db, $id, $pswd, $byteCoins) {
    $query = "UPDATE scouters SET byteCoins = byteCoins + ? WHERE id = ?";
    if(checkPassword($db, $id, $pswd)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("ii", $byteCoins, $id);
            $stmt->execute();
            return true;
        }
    }
    die ("Adding Byte Coins failed");
}

function removeByteCoins($db, $id, $pswd, $byteCoins) {
}

function getByteCoins($db, $id, $pswd) {
    $query = "SELECT byteCoins FROM scouters WHERE id = ?";
    if(checkPassword($db, $id, $pswd)) {
        //addByteCoins(getByteCoinsToAdd($db, $id));
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_array()) {
                $db->close();
                die(json_encode($row[0]));
            }
        }
    }
    die("Getting Byte Coins failed");
}
?>
