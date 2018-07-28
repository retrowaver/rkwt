class ItemService
{
	constructor(ItemDisplayService, dataContainer, preloader)
	{
		this._displayService = displayService;
		this._dataContainer = dataContainer;
		this._preloader = preloader;
	}

	removeItem(itemId)
	{
		this._preloader.show();
		$.getJSON('/ajax/item/remove/' + itemId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function() {
			this._displayService.removeItem(itemId);
		}, this)).done($.proxy(function(){
			this._preloader.hide();
		}, this));
	}
}