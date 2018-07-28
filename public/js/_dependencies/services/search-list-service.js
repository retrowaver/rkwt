class SearchListService
{
	constructor(displayService, dataContainer, preloader)
	{
		this._displayService = displayService;
		this._dataContainer = dataContainer;
		this._preloader = preloader;
	}

	removeSearch(searchId)
	{
		this._preloader.show();
		$.getJSON('/ajax/search/remove/' + searchId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function() {
			this._displayService.removeSearch(searchId);
		}, this)).done($.proxy(function(){
			this._preloader.hide();
		}, this));
	}
}