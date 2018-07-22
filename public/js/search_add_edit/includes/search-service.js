class SearchService
{
	constructor(filterCollection, filterService, displayService, dataContainer)
	{
		this._filterCollection = filterCollection;
		this._filterService = filterService;
		this._displayService = displayService; // ??????????????????????????
		this._dataContainer = dataContainer;
	}

	saveNewSearch()
	{
		var search = {
			name: this._dataContainer.filterData.name,
			filters: this._filterCollection.getFiltersForApi()
		};

		$.getJSON('/ajax/search/save', {search: search}, $.proxy(function(data) {
			if (!data.success) {
				this._displayService.displayError(data.error);
			}

			//redirect
		}, this));
	}

	saveEditedSearch(searchId)
	{
		var search = {
			name: this._dataContainer.filterData.name,
			filters: this._filterCollection.getFiltersForApi()
		};

		$.getJSON('/ajax/search/edit/' + this._dataContainer.filterData.id, {search: search}, $.proxy(function(data) {
			if (!data.success) {
				this._displayService.displayError(data.error);
			}
		}, this));
	}

	loadSearch(searchId)
	{
		$.getJSON('/ajax/search/get/' + searchId, {}, $.proxy(function(search) {

			//console.log(search);

			this._filterCollection.setValues(search.filtersForApi);

			this._dataContainer.filterData = {
				name: search.name,
				id: search.id
			}

			this._filterService.updateMeta(true);
			/*this._filterService.updateMeta([
				this._displayService.updateFiltersPicker,
				this._displayService.displayFilters
			]);*/
		
		}, this));
	}
}