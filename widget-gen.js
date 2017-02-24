var widget_gen_button = document.getElementById('bc-widget-gen-button');
var widget_gen_container = document.getElementById('bc-widget-gen-container');
var container_visible = false;
var widget_gen_pre = document.getElementById('bc-widget-gen-pre');

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

widget_gen_pre.addEventListener('click', selectAllHTML);
function selectAllHTML(event) {
	// taken from http://www.satya-weblog.com/2013/11/javascript-select-all-content-html-element.html
	var range, selection;

	if (document.body.createTextRange) { //ms
		range = document.body.createTextRange();
		range.moveToElementText(this);
		range.select();
	} else if (window.getSelection) { //all others
		selection = window.getSelection();
		range = document.createRange();
		range.selectNodeContents(this);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}