<?php
include('connect.php');
include('functions.php');
$teamNumber = $_POST['teamNumber'];
$scouterId = $_POST['id'];
$comment = isset($_POST['comment']) ? $_POST['comment'] : null;

if(isset($_FILES["files"])) {
    $picNums = array();
    if(!file_exists("../pics/")) {
        mkdir("../pics/");
    }
    if(!file_exists("../pics/$teamNumber")) {
        mkdir("../pics/$teamNumber");
    }
    foreach ($_FILES['files']['tmp_name'] as $picture) {
        $dir = scandir("../pics/$teamNumber");
        array_splice($dir, 0, 2);
        $dirLength = count($dir);
        $picNums[] = $dirLength + 1;
        resizeImage($picture, "../pics/$teamNumber/" . $picNums[count($picNums) - 1] . ".jpg");
    }
}

//Comments submission
if($comment != null && !isset($_FILES["files"])) {
    $query = "INSERT INTO pit_comments (team_number, pit_comments, id)
                VALUES (?, ?, ?)";
    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("iss", $teamNumber, 
                    $comment, 
                    $scouterId);
            $stmt->execute();
            $insert_id = $stmt->insert_id;
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
        die ( '{"message":"Failed creating statement"}' );
    }
}

if (isset($_FILES["files"]) && $comment == null) {
    $query = "INSERT INTO pit_pictures (team_number, id, pic_num)
                VALUES (?, ?, ?)";
    foreach ($picNums as $picNum) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("iii", $teamNumber, 
                        $scouterId,
                        $picNum);
                $stmt->execute();
                $insert_id = $stmt->insert_id;
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            $db->close();
            die ( '{"message":"Failed creating statement"}' );
        }
    }
}

if (isset($_FILES["files"]) && $comment != null) {
    //Insert pictures into database
    $query = "INSERT INTO pit_pictures (team_number, id, pic_num)
                VALUES (?, ?, ?)";
    foreach ($picNums as $picNum) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("iii", $teamNumber, 
                        $scouterId,
                        $picNum);
                $stmt->execute();
                $insert_id = $stmt->insert_id;
        } else {
            header('HTTP/1.1 500 SQL Error', true, 500);
            $db->close();
            die ( '{"message":"Failed creating statement"}' );
        }
    }
    
    //Insert comment into database
    $query = "INSERT INTO pit_comments (team_number, pit_comments, id)
                VALUES (?, ?, ?)";
    if($stmt = $db->prepare($query)){
        $stmt->bind_param("isi", $teamNumber, 
                    $comment, 
                    $scouterId);
            $stmt->execute();
            $insert_id = $stmt->insert_id;
    } else {
        header('HTTP/1.1 500 SQL Error', true, 500);
        $db->close();
        die ( '{"message":"Failed creating statement"}' );
    }
}

$db->close();
?>
