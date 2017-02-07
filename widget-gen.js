var widget_gen_button = document.getElementById('bc-widget-gen-button');
var widget_gen_container = document.getElementById('bc-widget-gen-container');
var container_visible = false;

widget_gen_button.addEventListener('click', toggleWidgetGenContainer);
function toggleWidgetGenContainer(event) {
	event.preventDefault();
	widget_gen_container.classList.toggle('bc_visible');
	if ( container_visible === false ) {
		container_visible = true;
		widget_gen_button.innerHTML = "Hide";
	} else {
		container_visible = false;
		widget_gen_button.innerHTML = "Embed";
	}

}