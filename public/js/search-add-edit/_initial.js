var initial = function() {
	dataContainer.currentFilter = null;
	dataContainer.searchData = {
		name: $.i18n('default-search-name')
	};


	var searchId = $("input[name='searchId']").val();
	if (typeof(searchId) === 'undefined') {
		// New search
		filterService.updateFilters();
	} else {
		// Editing existing search
		searchService.loadSearch(searchId);
	}
}