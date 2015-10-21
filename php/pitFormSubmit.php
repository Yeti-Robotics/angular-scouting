<?php
include('connect.php');
$teamNumber = $_POST['teamNumber'];
$scouterName = $_POST['name'];
$comment = $_POST['comment'];

if(count($postData["pictureRows"]["pictures"]) > 0) {
    $picNums = array();
    if(!file_exists("pics/")) {
        mkdir("pics/");
    }
    if(!file_exists("pics/".$teamNumber)) {
        mkdir("pics/" . $teamNumber);
    }
    foreach ($postData['pictureRows']['pictures'] as $picture) {
//        resizeImage($_FILES['RobotPicture']['tmp_name'], "pics/" . $teamNumber . "/" . ($dirLength + 1) . ".jpg");
        $dir = scandir("pics/".$teamNumber);
        array_splice($dir, 0, 2);
        $dirLength = count($dir);
        $picNums[] = $dirLength + 1;
        writePicture("pics/$teamNumber/".$picNums[count($picNums) - 1].".jpg", $picture["src"]);
    }
}

//
////Comments submission
//if(!empty($postData["comments"]) && count($postData["pictureRows"]["pictures"]) == 0) {
//    $query = "INSERT INTO pit_scouting (team_number, pit_comments, scouter_name)
//                VALUES (?, ?, ?)";
//    if($stmt = $db->prepare($query)){
//        $stmt->bind_param("iss", $postData["teamnumber"], 
//                    $postData["comments"], 
//                    $postData["scouter_name"]);
//            $stmt->execute();
//            $insert_id = $stmt->insert_id;
//        if ($insert_id > 0) {
//            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
//        } else {
//            echo "<h1>Upload failed. Please review your data and try again.</h1>";
//        }
//    }
//}
//
//if (count($postData["pictureRows"]["pictures"]) > 0 && empty($postData["comments"])) {
//    $query = "INSERT INTO pit_scouting (team_number, scouter_name, pic_num)
//                VALUES (?, ?, ?)";
//    if($stmt = $db->prepare($query)){
//        $stmt->bind_param("isi", $postData["teamnumber"], 
//                    $postData["scouter_name"],
//                    $picNums);
//            $stmt->execute();
//            $insert_id = $stmt->insert_id;
//        if ($insert_id > 0) {
//            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
//        } else {
//            echo "<h1>Upload failed. Please review your data and try again.</h1>";
//        }
//    }
//}
//
//if (count($postData["pictureRows"]["pictures"]) > 0 && !empty($postData["comments"])) {
//    $query = "INSERT INTO pit_scouting (team_number, scouter_name, pic_num, pit_comments)
//                VALUES (?, ?, ?, ?)";
//    if($stmt = $db->prepare($query)){
//        $stmt->bind_param("isis", $postData["teamnumber"], 
//                    $postData["scouter_name"],
//                    $picNums,
//                    $postData["comments"]);
//            $stmt->execute();
//            $insert_id = $stmt->insert_id;
//        if ($insert_id > 0) {
//            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/pit.php");
//        } else {
//            echo "<h1>Upload failed. Please review your data and try again.</h1>";
//        }
//    }
//}

function writePicture($fileName, $data) {
    if(file_exists($fileName)) {
        return false;
    }
    else {
        $data = substr($data, 23);
        $data = base64_decode($data);

        $im = imagecreatefromstring($data);
        header('Content-Type: image/jpeg');
        imagepng($im, $_SERVER["DOCUMENT_ROOT"]."/tmp/".basename($fileName));
        imagedestroy($im);
        
//        resizeImage();
    }
}
//$db->close();
?>
