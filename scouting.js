/**
 * 
 */
function addStackRow() {
	var node = document.getElementById("stack_row").cloneNode(true);
	node.style.display = "block";
	
	var container = document.getElementById("stack_row_container");
	var rowId = "stack_row_" + container.childNodes.length;
	node.id = rowId;
	var image = node.querySelector("#delete_button");
	image.onclick = function(){deleteStackRow(rowId)};
	container.appendChild(node);
}

function toggleAutonomous(checked) {
	var node = document.getElementById("autonmous_container");
	if (checked){
		node.style.display = "block";
	} else{
		node.style.display = "none";
	}
}



function deleteStackRow(rowId){
	var node = document.getElementById(rowId);
	node.parentNode.removeChild(node);
}
