const displayService = new DisplayService();
const searchService = new SearchService(displayService);

$(".remove-search").click(function() {
	var searchId = $(this).data("search-id");

	$.confirm({
	    title: 'Usuwanie wyszukiwania',
	    content: 'Na pewno usunąć to wyszukiwanie? Wszystkie znalezione za jego pomocą przedmioty zostaną usunięte z Twojego panelu użytkownika.',
	    buttons: {
	        confirm: {
	            text: 'Usuń',
	            btnClass: 'btn-danger',
	            keys: ['enter'],
	            //action: searchService.removeSearch(searchId)
	            action: function(){
	            	searchService.removeSearch(searchId)
	            }
	        },
	        cancel: {
	            text: 'Anuluj',
	            btnClass: 'btn',
	            keys: ['esc']
	        },
	    }
	});
});