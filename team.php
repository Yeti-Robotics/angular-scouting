<?php
include("header.php");
include ("connect.php");
include("functions.php");


$team = "";
$comments = [];
$names = [];
$timestamps = [];
$matchNumber = [];
$query = "SELECT team, comments, UNIX_TIMESTAMP(timestamp) AS timestamp, name, match_number
			FROM scout_data
			WHERE team = ?";

if($stmt = $db->prepare($query)){
	if (isset($_GET["team"])) {
		$stmt->bind_param("i", $_GET["team"]);
	}
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$team = $row["team"];
			if ($row["comments"] != "" && $row["comments"] != null) {
				$comments[] = $row["comments"];
				$timestamps[] = intval($row["timestamp"]);
				$names[] = $row["name"];
				$matchNumber[] = $row["match_number"];
			}
		}
	}
}
?>
		<h2 class="page_header">Team <?php echo $team;?></h2>
		<h3 class="pit_link"><a href="viewRobot.php?teamNumber=<?php echo $_GET['team'];?>">Pit Info</a></h3><br/><br/><br/>
<?php
$result = getTeamRankings($db, $team);
echo "<h3>Overall Results</h3>";
if ($result) {
	echo "<table border='1'>";
	$fields = $result->fetch_fields();
	echo "<tr>";
	foreach ($fields as $field) {
		echo "<th>".$field->name."</th>";
	}
	echo "</tr>";
	while ( $row = $result->fetch_assoc () ) {
		echo "<tr class=\"team_row\">";
		foreach ( $row as $key => $value ) {
			echo "<td>" . $value . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo ( "<h2>Query failed</h2>" );
}

$result = getTeamStacksTable($db, $team);
echo "<h3>Teleop</h3>";
echo "<h4>Stacks</h4>";
if ($result) {
	echo "<table border='1'>";
	$fields = $result->fetch_fields();
	echo "<tr>";
	foreach ($fields as $field) {
			echo "<th>".$field->name."</th>";
	}
	echo "</tr>";
	while ( $row = $result->fetch_assoc () ) {
		echo "<tr class=\"team_row\">";
		foreach ( $row as $key => $value ) {
			echo "<td>" . $value . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo ( "<h2>Query failed</h2>" );
}

$result = getTeamTotesOriginTable($db, $team);
echo "<h4>Origin of totes</h4>";
if ($result) {
	echo "<table border='1'>";
	$fields = $result->fetch_fields();
	echo "<tr>";
	foreach ($fields as $field) {
		echo "<th>".$field->name."</th>";
	}
	echo "</tr>";
	while ( $row = $result->fetch_assoc () ) {
		echo "<tr class=\"team_row\">";
		foreach ( $row as $key => $value ) {
			echo "<td>" . $value . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo ( "<h2>Query failed</h2>" );
}

$result = getTeamAutoTable($db, $team);
echo "<h3>Autonomous</h3>";
if ($result) {
	echo "<table border='1'>";
	$fields = $result->fetch_fields();
	echo "<tr>";
	foreach ($fields as $field) {
			echo "<th>".$field->name."</th>";
	}
	echo "</tr>";
	while ( $row = $result->fetch_assoc () ) {
		echo "<tr class=\"team_row\">";
		foreach ( $row as $key => $value ) {
			echo "<td>" . $value . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo ( "<h2>Query failed</h2>" );
}

$result = getTeamCoopertition($db, $team);
echo "<h3>Coopertition</h3>";
if ($result) {
	echo "<table border='1'>";
	$fields = $result->fetch_fields();
	echo "<tr>";
	foreach ($fields as $field) {
		echo "<th>".$field->name."</th>";
	}
	echo "</tr>";
	while ( $row = $result->fetch_assoc () ) {
		echo "<tr class=\"team_row\">";
		foreach ( $row as $key => $value ) {
			echo "<td>" . $value . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo ( "<h2>Query failed</h2>" );
}




$db->close();
?>

	
	<div>
		<h3>Comments:</h3>
		<?php 
			if (count($comments) == 0) {
				echo "<p class='team_comment'>No comments are available for this team.</p>";
			} else {
				foreach ($comments as $key => $comment) {
					echo "<p class='team_comment'>";
					echo $comment;
					echo "<br/>";
					echo "<span class='timestamp'>-- ".$names[$key].", ".timeAgo($timestamps[$key])." for match #".$matchNumber[$key]."</span>";
					echo "</p>";
				}
			}
		?>
	</div>
<?php 

	include("footer.php");
?>
