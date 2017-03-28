var widget_gen_button = document.getElementById('bc-widget-gen-button');
var widget_gen_container = document.getElementById('bc-widget-gen-container');
var container_visible = false;
var widget_gen_tabs = document.getElementsByClassName('embed-tab');
var widget_gen_sections = document.getElementsByClassName('bc-widget-gen__embed-section');
var widget_gen_pres = document.getElementsByClassName('bc-widget-gen__code');

widget_gen_button.addEventListener('click', toggleWidgetGenContainer);
for (var i = 0; i < widget_gen_pres.length; i++) {
	widget_gen_pres[i].addEventListener('click', selectAllHTML);
}
for (var i = 0; i < widget_gen_tabs.length; i++) {
	widget_gen_tabs[i].addEventListener('click', makeSectionVisible);
}

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

function makeSectionVisible(event) {
	event.preventDefault();

	var target_tab = event.target;
	target_tab.classList.add('embed-tab--selected');
	// Un-highlight other tabs
	for (var i = 0; i < widget_gen_tabs.length; i++) {
		if (widget_gen_tabs[i].dataset.section != target_tab.dataset.section) {
			widget_gen_tabs[i].classList.remove('embed-tab--selected');
		}
	}

	var target_section_id = target_tab.dataset.section;
	var target_section = document.getElementById(target_section_id);
	if (target_section) {
		target_section.classList.add('bc_visible');
		// Hide other sections
		for (i = 0; i < widget_gen_sections.length; i++) {
			if (widget_gen_sections[i].id != target_section_id) {
				widget_gen_sections[i].classList.remove('bc_visible');
			}
		}
	}
}