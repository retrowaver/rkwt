class SearchService
{
	constructor(filterCollection, filterService, displayService, dataContainer, preloader)
	{
		this._filterCollection = filterCollection;
		this._filterService = filterService;
		this._displayService = displayService;
		this._dataContainer = dataContainer;
		this._preloader = preloader;
	}

	saveSearch(searchId = null)
	{
		this._preloader.show();

		var search = {
			name: this._dataContainer.searchData.name,
			filters: this._filterCollection.getFiltersForApi()
		};

		console.log(search);

		// If no search ID specified then save data as a new search. Otherwise alter an existing one.
		var targetUri = (searchId === null) ? '/ajax/search/save' : '/ajax/search/edit/' + searchId;

		$.getJSON(targetUri, {search: search, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(data) {
			if (!data.success) {
				this._displayService.displayError(
					$.i18n.apply(this, data.error)
				);
			} else {
				window.location.href = $.i18n('route-search-list');
			}
		}, this)).done($.proxy(function(){
			this._preloader.hide();
		}, this));
	}

	loadSearch(searchId)
	{
		this._preloader.show();
		$.getJSON('/ajax/search/get/' + searchId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function(search) {
			this._filterCollection.setValues(search.filtersForApi);

			this._dataContainer.searchData = {
				name: search.name,
				id: search.id
			}

			this._filterService.updateFilters(true);
		}, this)).done($.proxy(function(){
			this._preloader.hide();
		}, this));
	}
}