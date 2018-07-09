class SearchService
{
	constructor(filterCollection)
	{
		this._filterCollection = filterCollection;
	}

	saveNewSearch()
	{

		var search = {
			name: 'untitled so far',
			filters: this._filterCollection.getFiltersForPhp()
		};

		$.getJSON('/ajax/search/save', {search: search}, $.proxy(function() {
			alert('paszlo');

			//LOADER END
		}, this));
	}
}