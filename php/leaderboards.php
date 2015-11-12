<?php
include ("connect.php");
header('Content-Type: application/json');
$query = "SELECT name, byteCoins FROM `scouters`";
if ($result = $db->query($query)) {
    $output = array();
    while ($row = $result->fetch_assoc()) {
        $output[] = $row;
    }
    echo(json_encode($output));
} else {
	$db->close ();
    header('HTTP/1.1 500 SQL Error', true, 500);
	die ( '{"error":"wasone"}' );
}
$db->close ();
?>
