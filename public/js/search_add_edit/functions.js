const templates = new Templates();
const filterCollection = new FilterCollection();
const displayService = new DisplayService(filterCollection, templates);
const filterService = new FilterService(filterCollection, displayService);
const searchService = new SearchService(filterCollection, filterService, displayService);

const dataContainer = {
	'currentFilter': null
};

//validation
$("#new-search-modal").on("change", "input, select", function() {
	if (filterService.validateFilter(dataContainer.currentFilter)) {
		displayService.enableSaveButton();
	} else {
		displayService.disableSaveButton();
	}
});

//
$(document).on("click", ".category-to-pick", function() {
	var categoryId = $(this).data("category-id");

	$("#category-id").val(categoryId);
	displayService.updateCategoryPickerTree(categoryId);
});

//
$(".save-new-search").click(function() {
	searchService.saveNewSearch();
});

$(".save-edited-search").click(function() {
	searchService.saveEditedSearch(
		$("input[name='searchId']").val()
	);
});

$(".save-filter").click(function() {
	
	var filterId = $(this).data("filterId");
	//var isRange = filterCollection.getMetaByIds([filterId])[0].filterIsRange;

	//console.log(formData);

	var values = filterService.getValuesFromForm();

	//console.log(values);

	//console.log(values);


	////////////////////////////////??DOPISAC SPRAWDZANIE, CZY FILTR O DANYM ID JUZ NIE ISTNIEJE <- to chyba nie ma sensu, lepiej nie wyswietlac takiego filtra na liscie
	/////////????ALSO DOPISAC SPRAWDZANIE, CZY WARTOSC / JEDNA Z WARTOSCI NIE JEST PUSTA


	//console.log(filterId);
	//console.log(values);


	if (filterService.validateFilter(filterId)) {
		filterService.saveFilter(filterId, values);
		$('#new-search-modal').modal('hide');
	}
});

/*$(".remove-filter").click(function() {
	var filterId = $(this).data("filterId");

	filterService.removeFilter(filterId);
});*/

$('#filters').on('click', '.edit-filter', function(){
	var filterId = $(this).data("filterid");
	
	//console.log(filterId);
	dataContainer.currentFilter = filterId;
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

	//
	dataContainer.currentFilter = filterId;


	displayService.displayModal(filterId);
});