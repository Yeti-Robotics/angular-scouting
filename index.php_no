<?php 
include('header.php');
?>
	<div>
		<form id="scouting_form" action="scouting_submit.php" method="POST">
			<fieldset>
				<legend>Info</legend>
				<div class="aligned_controls">
					<label for="name">Your Name:</label>
					<input type="text" name="name" placeholder="Enter your name" required="required"/><br/>
					<label>Match #:</label> 
					<input type="number" name="match_number" placeholder="Enter the match number" required="required"/><br/>
					<label>Team #:</label>  
					<input type="number" name="team_number" placeholder="Enter the team number" required="required"/><br/>
				</div>
			</fieldset>
			<fieldset>
				<legend>Autonomous</legend>
				<div class="aligned_controls">
					<label for="robot_moved">Did the robot move?</label>
					<input type="checkbox" name="robot_moved" onchange="toggleAutonomous(this.checked)"/><br/>
					<div id="autonmous_container" class="aligned_controls">
						<label for="totes_auto">How many totes in auto zone?</label>
						<select name="totes_auto" id="totes_auto">
							<option value="0" selected="selected">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
						<label for="cans_auto">How many cans in auto zone?</label>
						<select name="cans_auto" class="cans_dropdown">
							<option value="0" selected="selected">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
						<label for="cans_auto_origin">Grabs how many cans from step?</label>
						<select name="cans_auto_origin">
							<option value="0" selected="selected">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
						</select>
						<label for="in_auto_zone">Did it finish in the auto zone?</label>
						<input type="checkbox" name="in_auto_zone">
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Coopertition</legend>
				<div class="aligned_controls">
<!-- 					<label for="coopertition">Was coopertition attempted?</label> -->
<!-- 					<input type="checkbox" name="coopertition"/> -->
					<label for="coopertition_totes">How many totes?</label>
					<select name="coopertition_totes" id="coopertition_totes">
						<option value="0" selected="selected">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
			</fieldset>
			<fieldset>
				<legend>Totes</legend>
				<div class="aligned_controls">
					<label for="totes_from_landfill">Totes from landfill?</label>
					<input type="checkbox" name="totes_from_landfill"/>
					<label for="totes_from_human">Totes from human player?</label>
					<input type="checkbox" name="totes_from_human"/>		
				</div>
			</fieldset>
			<fieldset>
				<legend>Cans</legend>
				<div class="aligned_controls">					
					<label for="cans_from_middle">Grab cans from middle?</label>
					<input type="checkbox" name="cans_from_middle"/>
				</div>
			</fieldset>
			<fieldset>
				<legend>Stacks</legend>
				<div id="stack_row_container">
					<label for="add_button">Click to add a stack</label>
					<input type="button" value="Add" class="add_button" onclick="addStackRow()"/>
				</div>
			</fieldset>
			<fieldset>
				<legend>Misc.</legend>
				<div class="aligned_controls">
					<label for="rating">Rating:</label>
					<select name="rating">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
				</div>
				<div class="aligned_controls">
					<label for="score">Score:</label>
					<input type="number" name="score" placeholder="Enter the alliance's score" required="required"/>
				</div>
				<div class="aligned_controls">
					<label>Comments:</label><br/>
					<textarea name="comments" rows="5" required="required"></textarea>
		 		</div>
			</fieldset>
			<div class="submit_button_container">
				<input type="submit" value="Submit" class="submit_button"/>
			</div>
		</form>
		
		<!-- Stack row template -->
		<div id="stack_row" class="stack_row">
			<hr class="stack_row_divider"/>
			<label for="stacks_totes">Totes:</label>
			<select name="stacks_totes[]" class="totes_dropdown">
				<option value="0" selected="selected">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
			</select>
			<label for="capped_stack">Cap:</label>
			<select name="capped_stack[]" class="cans_dropdown">
				<option value="0" selected="selected">No cap</option>
				<option value="1">Cap w/o litter</option>
				<option value="2">Cap w/ litter</option>
			</select>
			<br/>
			<label for="cap_height">Cap height:</label>
			<select name="cap_height[]">
				<option value="0" selected="selected">(No cap)</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
			</select>
			<svg id="delete_button" height="15px" width="15px">
   				<line x1="0" y1="0" x2="15" y2="15" style="stroke:rgb(255,0,0);stroke-width:2"/>
  				<line x1="15" y1="0" x2="0" y2="15" style="stroke:rgb(255,0,0);stroke-width:2"/>
			</svg>
		</div>
	</div>
	<script>
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
	</script>
<?php 
	include("footer.php");
?>
