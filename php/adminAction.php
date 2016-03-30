<?php
include("connect.php");
include("functions.php");
$postData = json_decode(file_get_contents("php://input"), true);
if(isUserAdmin($db, $postData["token"])) {
	switch ($postData["action"]) {
		case 'update_wagers':
			updateQualificationWagers($db, $postData["matchNumber"]);
			break;
		case 'update_team':
			updateTeamInfo($db, $postData["teamNumber"]);
			break;
		case 'flush_schedule':
			flushSchedule();
			break;
		case 'updateSettings':
			$settings = getSettings();
			$settingName = $postData["setting"];
			$newSettingValue = $postData["settingValue"];
			
			$settings[$settingName] = $newSettingValue;
			updateSettings($settings);
			break;
	}
}
?>
