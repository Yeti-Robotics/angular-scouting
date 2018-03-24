<?php

include("functions.php");
include("connect.php");

echo(json_encode(getFutureMatches($db)));

?>
