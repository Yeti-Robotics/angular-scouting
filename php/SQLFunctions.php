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
                return $row[0];
            }
        }
    }
    else {
        return false;
    }
}
?>
