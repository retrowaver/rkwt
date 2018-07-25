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
	            action: function(){
	            	searchListService.removeSearch(searchId)
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