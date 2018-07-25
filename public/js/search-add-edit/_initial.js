var initial = function() {
	dataContainer.currentFilter = null;
	dataContainer.filterData = {
		name: 'bez nazwy'
	};


	var searchId = $("input[name='searchId']").val();
	if (typeof(searchId) === 'undefined') {
		/////// if new:
		filterService.updateMeta();
	} else {
		/////// if edit:
		searchService.loadSearch(searchId);
	}
}