<?php 
	include ('header.php');
?>
	<form name='pit' action="full_pit_submit.php" method="POST" enctype="multipart/form-data">
	<input type='number' name='teamnumber' id='teamnumber' placeholder='Enter Team Number here' required>
	<input id="scouter_name" type="text" name='scouter_name' placeholder='Please enter your name' required>
	<fieldset>
		<img alt="Please Upload a File!" id='displayarea'>
		<br>
		<br>
		<legend>Upload Pictures here</legend>
		<input type="file" id='robotimage' name="RobotPicture">
		<br>
	</fieldset>
	<fieldset>
		<legend>Type Your comments here</legend>
		<textarea id="comments" rows="3" cols="40" name='comments' placeholder='Enter comments about the team here'></textarea>
		<br>
	</fieldset>
	<div class="submit_button_container">
		<input id="pit_submit" type="submit" value="Submit" class="submit_button"/>
	</div>
	</form>

	<script>
	var reader = new FileReader();
	reader.onload = function() {
		rawData = reader.result;
		display.src = rawData;
		display.setAttribute("height", "auto");
	}
	
	var display = document.getElementById('displayarea');
	var teamNumber = document.getElementById('teamnumber');
	var fileInput = document.getElementById('robotimage');
	var nameField = document.getElementById('scouter_name');
	var commentsInput = document.getElementById('comments');
	var inputs = document.getElementsByTagName("input");
	var filledFields = 0;
	var submitButton = document.getElementById("pit_submit");
	var commentText = document.getElementsByTagName("textarea")[0];

	commentsInput.oninput = function() {
		if (commentsInput.value != "") {
			nameField.setAttribute("required", "required");
		} else {
			nameField.removeAttribute("required");
		}
	};
	
	fileInput.onchange = function(e) {
		reader.readAsDataURL(e.target.files[0]);
	};

	var forms = document.getElementsByTagName('form');
	for (var i = 0; i < forms.length; i++) {
	    forms[i].noValidate = true;
	    forms[i].addEventListener('submit', function(event) {
	        if (!event.target.checkValidity()) {
	            event.preventDefault();
	            alert("Looks like some fields have some invalid data. Why would that be?");
	        }
	    }, false);
	}

	submitButton.onclick = function(event) {
		if (inputs[1].value != "" && inputs[2].value == "" && inputs[3].value == "" && commentText.value == "") {
			if(event.preventDefault) {
				event.preventDefault();
			}
			else {
				event.returnValue = false;
			}
			alert('What are you doing? Trying to only submit a team number. What good does that do?');
		}
	}
	
	</script>
<?php 
	include('footer.php');
?>
