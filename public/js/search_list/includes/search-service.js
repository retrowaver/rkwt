class SearchService
{
	constructor(displayService)
	{
		this._displayService = displayService;
	}

	removeSearch(searchId)
	{
		$.getJSON('/ajax/search/remove/' + searchId, null, $.proxy(function() {
			this._displayService.removeSearch(searchId);
		}, this));
	}
}