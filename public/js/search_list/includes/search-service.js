class SearchService
{
	constructor(displayService, dataContainer)
	{
		this._displayService = displayService;
		this._dataContainer = dataContainer;
	}

	removeSearch(searchId)
	{
		$.getJSON('/ajax/search/remove/' + searchId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function() {
			this._displayService.removeSearch(searchId);
		}, this));
	}
}