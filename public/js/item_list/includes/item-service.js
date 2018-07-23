class ItemService
{
	constructor(displayService, dataContainer)
	{
		this._displayService = displayService;
		this._dataContainer = dataContainer;
	}

	removeItem(itemId)
	{
		$.getJSON('/ajax/item/remove/' + itemId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function() {
			this._displayService.removeItem(itemId);
		}, this));
	}
}