<?php

include("functions.php");
include("connect.php");

echo(json_encode(array("Schedule" => getFutureMatches($db))));

?>