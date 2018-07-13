class DisplayService
{
	removeItem(itemId)
	{
		$('#item-list').find('tr[data-item-id="' + itemId + '"]').fadeOut();
	}
}