class SearchListDisplayService
{
	removeSearch(searchId)
	{
		$('#search-list').find('tr[data-search-id="' + searchId + '"]').fadeOut();
	}
}