class SearchService
{
	constructor(filterCollection, filterService, displayService)
	{
		this._filterCollection = filterCollection;
		this._filterService = filterService; ////////////////////// MAY NOT BE USED
		this._displayService = displayService; // same story
	}

	saveNewSearch()
	{
		var search = {
			name: 'untitled so far',
			filters: this._filterCollection.getFiltersForApi()
		};

		$.getJSON('/ajax/search/save', {search: search}, $.proxy(function() {
			alert('paszlo');

			//LOADER END
		}, this));
	}

	saveEditedSearch(searchId)
	{
		var search = {
			name: 'najnowszy edit',
			filters: this._filterCollection.getFiltersForApi()
		};

		$.getJSON('/ajax/search/edit/' + searchId, {search: search}, $.proxy(function() {
			alert('paszlo edit');

			//LOADER END
		}, this));
	}

	loadSearch(searchId)
	{
		$.getJSON('/ajax/search/get/' + searchId, {}, $.proxy(function(search) {

			this._filterCollection.setValues(search.filtersForApi);

			this._filterService.updateMeta();
		
		}, this));
	}
}