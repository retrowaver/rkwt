$(document).on("submit", ".new-search-form", function(event){
	event.preventDefault();
});

$(".change-search-name").click(function() {
	displayService.openChangeSearchNameInput(
		dataContainer.searchData.name
	);
});

$(".change-search-name-input").focusout(function() {
	var newSearchName = $(this).val().trim();

	if (newSearchName.length > 0 && newSearchName.length <= 40) {
		dataContainer.searchData.name = newSearchName;
		displayService.closeChangeSearchNameInput(
			validator.escape(newSearchName)
		);	
	} else {
		displayService.closeChangeSearchNameInput(
			validator.escape(dataContainer.searchData.name)
		);
	}
});

$("#new-search-modal").on("input", "#user-id-picker-username", function() {
	displayService.disableSaveButton();
});

$("#new-search-modal").on("input", "#user-id-picker-username", $.debounce(500, function() {
	var username = $("#user-id-picker-username").val();
	filterService.getUserIdByUsername(username);
}));

$("#new-search-modal").on("input", ".validate-filter", function() {
	if (filterService.validateFilter(dataContainer.currentFilter)) {
		displayService.enableSaveButton();
	} else {
		displayService.disableSaveButton();
	}
});

$(document).on("click", ".category-to-pick", function() {
	var categoryId = $(this).data("category-id");

	$("#category-id").val(categoryId);
	displayService.updateCategoryPickerTree(categoryId);
});

$(".save-new-search").click(function() {
	searchService.saveSearch();
});

$(".save-edited-search").click(function() {
	searchService.saveSearch(
		dataContainer.searchData.id
	);
});

$(".show-filter-picker").click(function() {
	$("#filter-picker-modal").modal('show');
});

$(".save-filter").click(function() {
	var filterId = $(this).data("filterId");
	var values = filterService.getValuesFromForm();

	filterService.saveFilter(filterId, values);
	filterService.updateFilters();

	$('#new-search-modal').modal('hide');
});

$('#filters').on('click', '.edit-filter', function(){
	var filterId = $(this).data("filterid");
	
	dataContainer.currentFilter = filterId;
	displayService.displayModal(filterId, true);
}); 

$('#filters').on('click', '.remove-filter', function(){
	var filterId = $(this).data("filterid");
	
    filterService.removeFilter(filterId);
	filterService.updateFilters();
}); 

$("#filters-container").change(function() {
	// Get filter id from option value
	var filterId = $(this).val();

	dataContainer.currentFilter = filterId;

	$("#filter-picker-modal").modal('hide');
	displayService.displayModal(filterId);

	// Reset select
	$(this).prop('selectedIndex', 0);
});