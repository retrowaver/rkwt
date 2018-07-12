$(".remove-search").click(function() {
	var searchId = $(this).data("search-id");

	$.getJSON('/ajax/search/remove/' + searchId, null, function() {
		

		alert('dopisac usuwanie wiersza jak wymysle jak ma wygladac');

		//LOADER END
	});
});