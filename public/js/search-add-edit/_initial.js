var initial = function() {
	dataContainer.currentFilter = null;
	dataContainer.searchData = {
		name: $.i18n('default-search-name')
	};


	var searchId = $("input[name='searchId']").val();
	if (typeof(searchId) === 'undefined') {
		/////// if new:
		filterService.updateFilters();
	} else {
		/////// if edit:
		searchService.loadSearch(searchId);
	}
}