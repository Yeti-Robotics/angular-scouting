<?php 
	include('header.php');
	include('functions.php');
	include('connect.php');

	$comments = [];
	$picCredit = [];
	$picNum = [];
	$picTimestamps = [];
	$picNames = [];
	
	$result = getPitComments($db, $_GET['teamNumber']);
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			if ($row["Pit Scouters Comments"] != "" && $row["Pit Scouters Comments"] != null) {
				$comments[] = $row["Pit Scouters Comments"];
				$timestamps[] = intval($row["timestamp"]);
				$names[] = $row["Pit Scouter"];
			}
		}
	}
	
	$result = getPicInfo($db, $_GET['teamNumber']);
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$picNum[] = $row["Picture Number"];
			$picTimestamps[] = intval($row["timestamp"]);
			$picNames[] = $row["Pit Scouter"];
		}
	}
	
	for ($i = 0; $i < count($picNum); $i++) {
		$picCredit[$i] = "Submitted by $picNames[$i] " . timeAgo($picTimestamps[$i]);
	}
	
	$db->close();
?>
<center>
	<span id='pic_num'>
	</span>
</center>
<span id='left_arrow' onclick='previousPicture()'>
	&larr; Previous Picture
</span>
<span id='right_arrow'onclick='nextPicture()'>
	Next Picture &rarr;
</span>
<div id='img_div'>
	<img id='picture' src='' alt="What? Where's the picture?!?">
	<p id="pic_credit">
	</p>
</div>
<hr/>
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
				echo "<span class='timestamp'>-- ".$names[$key].", ".timeAgo($timestamps[$key])."</span>";
				echo "</p>";
			}
		}
	?>
</div>
<script>
	var picNum = 1;	
	var teamNumber = <?php echo json_encode($_GET['teamNumber']);?>;
	var picCredit = <?php 
						echo json_encode($picCredit);
					?>;
	var pics = <?php
					$dir = scandir("pics/".$_GET['teamNumber']);
					array_splice($dir, 0, 2);
					echo json_encode($dir);				
				?>;
	for (var i = 0; i < pics.length; i++) {
		pics[i] = "pics/" + teamNumber + "/" + pics[i];
	}
	var picLimit = pics.length;
	var picture;
	var angle = 0;
	var ogOrientation = true;
	var leftPadding = parseFloat(window.getComputedStyle(document.body, null).getPropertyValue('padding-left'));
	var rightPadding = parseFloat(window.getComputedStyle(document.body, null).getPropertyValue('padding-right'));
	var paddingWidth = leftPadding + rightPadding;
	var width = document.width - paddingWidth;
	var picCreditText = document.getElementById("pic_credit");

	picture = document.getElementById("picture");
	if (picLimit > 1) {
		picture.setAttribute("src", pics[0]);
		picCreditText.innerHTML = picCredit[0];
		refreshPicNum();
		setPictureWidth();
	}
	else if(picLimit == 1) {
		picture.setAttribute("src", pics[0]);
		picCreditText.innerHTML = picCredit[0];
		refreshPicNum();
		setPictureWidth();
		
		document.getElementById("left_arrow").innerHTML = "";
		document.getElementById("right_arrow").innerHTML = "";
		document.getElementById("img_div").innerHTML += "<br><center><h3>No more pictures are available for this team</h3></center>";
	}
	else {
		document.getElementById("left_arrow").innerHTML = "";
		document.getElementById("right_arrow").innerHTML = "";
		document.getElementById("img_div").innerHTML = "<center><h3>No pictures are available for this team</h3></center>";
	}
	
	function previousPicture() {
		if (picNum > 1) {
			picNum--;
		} else {
			picNum = picLimit;
		}
		picture.setAttribute("src", pics[picNum - 1]);
		picCreditText.innerHTML = picCredit[picNum - 1];
		refreshPicNum();
	}

	function nextPicture() {
		if (picNum < picLimit) {
			picNum++;
		} else {
			picNum = 1;
		}
		picture.setAttribute("src", pics[picNum - 1]);
		picCreditText.innerHTML = picCredit[picNum - 1];
		refreshPicNum();
	};
	
	function refreshPicNum() {
		document.getElementById("pic_num").innerHTML = picNum + "/" + picLimit;
	}

	function setPictureWidth() {
		picture.setAttribute("width", width);
		picture.removeAttribute("height");
	}
</script>
</body>
</html>
