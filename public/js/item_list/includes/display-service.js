class DisplayService
{
	removeItem(itemId)
	{
		$('#item-list').find('tr[data-item-id="' + itemId + '"]').fadeOut();
	}

	hideLoader()
	{
		$("#loading").hide();
	}

	showLoader()
	{
		$("#loading").show();
	}
}