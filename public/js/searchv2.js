$( document ).ready(function() {
	const templates = new Templates();
	const filterCollection = new FilterCollection();
	const displayService = new DisplayService(filterCollection, templates);
	const filterService = new FilterService(filterCollection, displayService);
	const searchService = new SearchService(filterCollection);


	//
	filterCollection.addValues('category',[89510]);
	filterCollection.setMeta([{
		'filterId': 'category',
		'filterName': 'Kategoria',
		'filterType': 'country',
		'filterControlType': 'textbox',
		'filterDataType': 'int',
		'filterIsRange': false,
	}]);
	//console.log(filterCollection);
	//




	filterService.updateMeta();

	//pomyslec nad podzieleniem jakims kodu, zeby nie trzeba bylo go powielac przy edycji searcha

	$(".save-new-search").click(function() {
		searchService.saveNewSearch();
	});

	$(".save-filter").click(function() {
		var formData = $('#new-search-form').serializeArray();
		var filterId = $(this).data("filterId");

		//console.log(formData);

		var values = [];
		$.each(formData, function() {
			values.push(this.value);
		});

		//console.log(values);


		////////////////////////////////??DOPISAC SPRAWDZANIE, CZY FILTR O DANYM ID JUZ NIE ISTNIEJE <- to chyba nie ma sensu, lepiej nie wyswietlac takiego filtra na liscie
		/////////????ALSO DOPISAC SPRAWDZANIE, CZY WARTOSC / JEDNA Z WARTOSCI NIE JEST PUSTA


		//console.log(filterId);
		//console.log(values);



		filterService.saveFilter(filterId, values);
		//console.log(filterCollection);




		//console.log(filterCollection);
	});

	/*$(".remove-filter").click(function() {
		var filterId = $(this).data("filterId");

		filterService.removeFilter(filterId);
	});*/

	$('#filters').on('click', '.edit-filter', function(){
		var filterId = $(this).data("filterid");
		
		//console.log(filterId);
		displayService.displayModal(filterId, true);
	}); 

	$('#filters').on('click', '.remove-filter', function(){
		var filterId = $(this).data("filterid");
		
		//console.log(filterId);
	    filterService.removeFilter(filterId);
	}); 

	$(".new-filter").click(function() {
		// Get filter id from option value
		var filterId = $(this).prev().val();

		// Do nothing if default (placeholder) option was chosen
		if (!filterId) {
			alert('Najpierw wybierz rodzaj filtra'); //robocze
			return true;
		}

		displayService.displayModal(filterId);
	});	
});