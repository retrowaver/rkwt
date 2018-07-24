class DisplayService
{
	removeSearch(searchId)
	{
		$('#search-list').find('tr[data-search-id="' + searchId + '"]').fadeOut();
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