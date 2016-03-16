<?php
include("connect.php");
include("functions.php");
$postData = json_decode(file_get_contents("php://input"), true);

if($id = getUserIdFromToken($db, $postData["token"])) {
	if($postData["matchPredicted"] >= nextMatch()) {
		if($postData["wagerType"] == "alliance") {
			$query = "INSERT INTO wagers (associatedId, wagerType, wageredByteCoins, matchPredicted, alliancePredicted)
			VALUES (?, ?, ?, ?, ?)";
			if($stmt = $db->prepare($query)){
				$stmt->bind_param("isiis",
					$id,
					$postData["wagerType"],
					$postData["wageredByteCoins"],
					$postData["matchPredicted"],
					$postData["alliancePredicted"]);
				$stmt->execute();
				echo("Alliance wager sent successfully!");
			}
			else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
				die('{"error": "Failed to upload alliance"}');
			}
		} else if($postData["wagerType"] == "closeMatch") {
			if($postData["withenPoints"] > -1 && $postData["withenPoints"] < 51) {
				$query = "INSERT INTO wagers (associatedId, wagerType, wageredByteCoins, matchPredicted, withenPoints)
				VALUES (?, ?, ?, ?, ?)";
				if($stmt = $db->prepare($query)){
					$stmt->bind_param("isiii",
						$id,
						$postData["wagerType"],
						$postData["wageredByteCoins"],
						$postData["matchPredicted"],
						$postData["withenPoints"]);
					$stmt->execute();
					echo("Close match wager sent successfully!");
				} else {
			  		header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
					die("{'message':'Failed to upload closeMatch'}");
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . '403 Bad headers', true, 403);
				die("{'message':'Wager parameters outside bounds'}");
			}
		} else if($postData["wagerType"] == "points") {
			if($postData["withenPoints"] > 109 && $postData["withenPoints"] < 301) {
				$query = "INSERT INTO wagers (associatedId, wagerType, wageredByteCoins, matchPredicted, alliancePredicted, withenPoints)
				VALUES (?, ?, ?, ?, ?, ?)";
				if($stmt = $db->prepare($query)) {
					$stmt->bind_param("isiisi",
						$id,
						$postData["wagerType"],
						$postData["wageredByteCoins"],
						$postData["matchPredicted"],
						$postData["alliancePredicted"],
						$postData["withenPoints"]);
					$stmt->execute();
					echo("Points wager sent successfully!");
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . '403 Bad headers', true, 403);
				die("{'message':'Wager parameters outside bounds'}");
			}
		} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 SQL Error', true, 500);
				die("Failed to upload points");
		}
		$query = "UPDATE scouters SET byteCoins=byteCoins-? WHERE id=?";
		if($stmt = $db->prepare($query)) {
			$stmt->bind_param("ii", $postData["wageredByteCoins"], $id);
			$stmt->execute();
			return true;
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . '403 Bad headers', true, 403);
		die("{'message':'Match wagered has already been completed'}");
	}
}
$db->close();
?>
