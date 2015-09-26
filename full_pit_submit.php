<?php 
	include('connect.php');
	include('functions.php');
	
	//Picture submition
	$teamNumber = $_POST['teamnumber'];
	$picNum = 0;

	if($_FILES["RobotPicture"]["size"] > 0) {
		if(!file_exists("pics/")) {
			mkdir("pics/");
		}
		if(!file_exists("pics/".$teamNumber)) {
			mkdir("pics/" . $teamNumber);
		}
		$dir = scandir("pics/".$teamNumber);
		array_splice($dir, 0, 2);
		$dirLength = count($dir);
		resizeImage($_FILES['RobotPicture']['tmp_name'], "pics/" . $teamNumber . "/" . ($dirLength + 1) . ".jpg");
		header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
	}
	
	//Comments submition
	if(!empty($_POST["comments"]) && $_FILES["RobotPicture"]["size"] == 0) {
		$query = "INSERT INTO pit_scouting (team_number, pit_comments, scouter_name)
					VALUES (?, ?, ?)";
		if($stmt = $db->prepare($query)){
			$stmt->bind_param("iss", $_POST["teamnumber"], 
						$_POST["comments"], 
						$_POST["scouter_name"]);
				$stmt->execute();
				$insert_id = $stmt->insert_id;
			if ($insert_id > 0) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
			} else {
				echo "<h1>Upload failed. Please review your data and try again.</h1>";
			}
		}
	}
		
	if ($_FILES["RobotPicture"]["size"] > 0 && empty($_POST["comments"])) {
		$query = "INSERT INTO pit_scouting (team_number, scouter_name, pic_num)
					VALUES (?, ?, ?)";
		if($stmt = $db->prepare($query)){
			$stmt->bind_param("isi", $_POST["teamnumber"], 
						$_POST["scouter_name"],
						$picNum);
				$stmt->execute();
				$insert_id = $stmt->insert_id;
			if ($insert_id > 0) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
			} else {
				echo "<h1>Upload failed. Please review your data and try again.</h1>";
			}
		}
	}
	
	if ($_FILES["RobotPicture"]["size"] > 0 && !empty($_POST["comments"])) {
		$query = "INSERT INTO pit_scouting (team_number, scouter_name, pic_num, pit_comments)
					VALUES (?, ?, ?, ?)";
		if($stmt = $db->prepare($query)){
			$stmt->bind_param("isis", $_POST["teamnumber"], 
						$_POST["scouter_name"],
						$picNum,
						$_POST["comments"]);
				$stmt->execute();
				$insert_id = $stmt->insert_id;
			if ($insert_id > 0) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
			} else {
				echo "<h1>Upload failed. Please review your data and try again.</h1>";
			}
		}
	}
	$db->close();
	
	echo "<h2 class='link' onclick='history.back()'>Back</h2>";
?>
