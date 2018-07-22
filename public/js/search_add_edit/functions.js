const dataContainer = {
	currentFilter: null,
	filterData: {
		name: 'bez nazwy'
	}
};

const templates = new Templates();
const filterCollection = new FilterCollection();
const displayService = new DisplayService(filterCollection, templates);
const filterService = new FilterService(filterCollection, displayService);
const searchService = new SearchService(filterCollection, filterService, displayService, dataContainer);


$(".change-search-name").click(function() {
	var searchName = dataContainer.filterData.name;

	displayService.openChangeSearchNameInput(searchName);
});

$(".change-search-name-input").focusout(function() {
	var newSearchName = $(this).val();

	if (newSearchName.trim().length > 0 && newSearchName.trim().length <= 40) {
		dataContainer.filterData.name = validator.escape(newSearchName.trim());
		displayService.closeChangeSearchNameInput(newSearchName.trim());	
	} else {
		displayService.closeChangeSearchNameInput(dataContainer.filterData.name);
	}
});






$("#new-search-modal").on("input", "#user-id-picker-username", function() {
	displayService.disableSaveButton();
});

$("#new-search-modal").on("change", "#user-id-picker-username", function() {
	var username = $("#user-id-picker-username").val();

	//but also validate & insert & change buttons

	//displayService.disableSaveButton();
	filterService.getUserIdByUsername(username);
	//console.log(userId);
});







//validation
$("#new-search-modal").on("change", ".validate-filter", function() {
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

$(".show-filter-picker").click(function() {
	$("#filter-picker-modal").modal('show');
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


	//if (filterService.validateFilter(filterId)) {
	filterService.saveFilter(filterId, values);
	displayService.updateDescription(filterId);




	//// THIS SHOULD BE DONE ONLY FOR A NUMBER OF FILTERS
	filterService.updateMeta();
	/////

	$('#new-search-modal').modal('hide');
	//}
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

    //// THIS SHOULD BE DONE ONLY FOR A NUMBER OF FILTERS
	filterService.updateMeta();
	/////
}); 

/*$(".back-to-filters").click(function() {
	$("#new-search-modal").modal('hide');
	$("#filter-picker-modal").modal('show');
});*/

$(".new-filter").click(function() {
	// Get filter id from option value
	var filterId = $("#filters-container").val();

	// Do nothing if default (placeholder) option was chosen
	if (!filterId) {
		alert('Najpierw wybierz rodzaj filtra'); //robocze
		return true;
	}

	//
	dataContainer.currentFilter = filterId;


	$("#filter-picker-modal").modal('hide');
	displayService.displayModal(filterId);
});