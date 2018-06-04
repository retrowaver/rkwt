$( document ).ready(function() {
	//alert('test');

	//data = '666';


	var filters = {
		basic: [
			{'filterId': 'category', 'filterValueId': [76102]}
		],
		country: [],
		category: []
	};
	var availableFilters = [];

	//console.log(filters);

	$.getJSON('/ajax/search/filters', {"filters": filters}, function (data) {
		availableFilters = data;
		console.log(availableFilters);
	});

	function updateFilters(currentFilters)
	{
		
	}

	//console.log(availableFilters);


});