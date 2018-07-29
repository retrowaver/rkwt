$(document).ready(function(){
	// Get CSRF token and store it in data container
	dataContainer.csrfToken = $("input[name='csrf-token']").val();

	// Set & load language plugin
	$.i18n({
		locale: dataContainer.locale
	});

	$.i18n().load({
		'pl': '/js/_translations/pl.json',
		//'en': '/js/_translations/en.json'
	}).done(function() {
		// Do some initial work if needed, e.g. load some stuff through AJAX
		if (typeof(initial) !== 'undefined') {
			initial();
		}
	});
});