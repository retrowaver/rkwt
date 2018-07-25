class SearchService
{
	constructor(filterCollection, filterService, displayService, dataContainer)
	{
		this._filterCollection = filterCollection;
		this._filterService = filterService;
		this._displayService = displayService;
		this._dataContainer = dataContainer;
	}

	saveSearch(searchId = null)
	{
		var search = {
			name: this._dataContainer.filterData.name,
			filters: this._filterCollection.getFiltersForApi()
		};

		// If no search ID specified then save data as a new search. Otherwise alter an existing one.
		var targetUri = (searchId === null) ? '/ajax/search/save' : '/ajax/search/edit/' + searchId;

		$.getJSON(targetUri, {search: search, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(data) {
			if (!data.success) {
				this._displayService.displayError(data.error);
			} else {
				window.location.href = '/search/list';
			}
		}, this));
	}

	loadSearch(searchId)
	{
		$.getJSON('/ajax/search/get/' + searchId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function(search) {
			this._filterCollection.setValues(search.filtersForApi);

			this._dataContainer.filterData = {
				name: search.name,
				id: search.id
			}

			this._filterService.updateMeta(true);
		
		}, this));
	}
}