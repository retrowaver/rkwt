class FilterService
{
	constructor(filterCollection, displayService)
	{
		this._filterCollection = filterCollection;
		this._displayService = displayService;
	}

	updateMeta()
	{
		var currentFilters = this._filterCollection.getFiltersForApiRequest();

		//console.log(currentFilters);

		$.getJSON('/ajax/search/filters', {currentFilters}, $.proxy(function(filters) {
			// Save received filters
			this._filterCollection.setMeta(filters.available);

			// Update displayed filters picker (so it will show updated filters)
			this._displayService.updateFiltersPicker();

			//LOADER END
		}, this));
	}

	saveFilter(filterId, values)
	{
		//add filter to collection

		//console.log(filterId);
		//console.log(values);

		this._filterCollection.addValues(filterId, values);

		//add filter to display

		this._displayService.addFilter(filterId);
	}

	removeFilter(filterId)
	{
		//remove filter from collection
		this._filterCollection.removeValues(filterId);

		//remove filter from display
		this._displayService.removeFilter(filterId);
	}
}