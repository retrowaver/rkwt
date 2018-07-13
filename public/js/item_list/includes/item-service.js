class ItemService
{
	constructor(displayService)
	{
		this._displayService = displayService;
	}

	removeItem(itemId)
	{
		$.getJSON('/ajax/item/remove/' + itemId, null, $.proxy(function() {
			this._displayService.removeItem(itemId);
		}, this));
	}
}