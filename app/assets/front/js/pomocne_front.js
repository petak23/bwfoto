window.onload = function() {
	let colArt = document.getElementById("colArt")
	if (colArt !== null) { // Len ak sa na stránke nachádza...
		colArt.addEventListener("click", function() {
			this.classList.add("d-none");	
		});
	}
}