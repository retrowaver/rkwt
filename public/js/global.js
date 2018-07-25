// Get CSRF token and store it in data container
dataContainer.csrfToken = $("input[name='csrf-token']").val();

// Set & load language plugin
$.i18n({
	locale: 'pl'
});

$.i18n().load({
	'pl': 'js/translation/pl.json'
}).done(function() {
	// Do some initial work if needed, e.g. load some stuff through AJAX
	if (typeof(initial) !== 'undefined') {
		initial();
	}
});