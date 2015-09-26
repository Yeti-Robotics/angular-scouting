<?php
function checkPassword($db, $id, $pswd) {
    $query = "";

    if($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        if (stmt->get_result() == $pswd){
            return true;
        }
        return false;
    }
}

function getName($db, $id, $pswd) {
    $query = "";
    if (checkPassword($db, $id, $pswd)) {
        if($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $id);
            $stmt->execute();
            return stmt->get_result();
        }
        else {
            return null;
        }
    }
}
?>
