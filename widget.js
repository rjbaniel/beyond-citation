var widgetPlaceholders = document.getElementsByClassName('bc-widget');

for (var i = 0; i < widgetPlaceholders.length; i++) {
	var database_name = widgetPlaceholders[i].dataset.database;
	if (storageAvailable('localStorage')) {
		// Uncomment the line below and reload the page when you need to bust the localStorage cache.
		//localStorage.removeItem(database_name + '_widget');
		if (localStorage.getItem(database_name + '_widget') && widget_localStorage_is_valid(database_name)) {
			widgetPlaceholders[i].innerHTML = localStorage.getItem(database_name + '_widget');
		} else {
			request_widget_html(database_name, i);
		}
	} else {
		request_widget_html(database_name, i);
	}
}

function request_widget_html(database_name, index) {
	var bc_xhr = new XMLHttpRequest();
	/*
		We need to pass the database name and the index of the current placeholder,
		while avoiding using an anonymomus function for addEventListener. So we'll just add them
		to the bc_xhr object.
	*/
	bc_xhr.database_name = database_name;
	bc_xhr.placeholder_index = index;
	bc_xhr.addEventListener('load', insert_widget_html);
	bc_xhr.open("GET", "https://beyondcitation.org/wp-json/beyond_citation/v1/databases/" + database_name);
	bc_xhr.send();
}
function insert_widget_html() {
	database_name = this.database_name;
	placeholder_index = this.placeholder_index;
	// Send along 'this', the XHR, so that we can have access to response and database_name
	widgetHTML = create_widget_html(this);
	if (widgetHTML) {
		widgetPlaceholders[placeholder_index].innerHTML = widgetHTML;
		if (storageAvailable('localStorage')) {
			localStorage.setItem(database_name + '_widget', widgetHTML);
			localStorage.setItem(database_name + '_widget_timestamp', Date.now());
		}
	}
}
function create_widget_html(bc_xhr) {
	var response = JSON.parse(bc_xhr.response);
	if (response === null) {
		console.log('Sorry, we weren\'t able to find the database with ID: "' + bc_xhr.database_name + '".');
		return;
	} else {
		var html = "<h3>" + response.title + "</h3>";
		html += "<p><strong>Date Range: </strong>" + response.date + "<br>";
		html += "<strong>Publisher: </strong>" + response.publisher + "</p>";
		html += "<p>" + response.overview + "</p>";
		html += '<a href="' + response.uri + '">See more about ' + response.title + '</a>';
		// This will be replaced by real code to create the HTML for the widget.
		return html;
	}
}

//Helpers
function widget_localStorage_is_valid(database_name) {
	if (Date.now() - localStorage.getItem(database_name + '_widget_timestamp') < 24 * 60 * 60 * 1000) {
		return true;
	}
	return false;
}
function storageAvailable(type) {
	try {
		var storage = window[type];
		var x = '__storage__test__';
		storage.setItem(x, x);
		storage.removeItem(x);
		return true;
	} catch(e) {
		return false;
	}
}