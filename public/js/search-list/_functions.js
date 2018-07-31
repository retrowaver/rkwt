$(".remove-search").click(function() {
	var searchId = $(this).data("search-id");

	$.confirm({
	    title: $.i18n('label-search-deletion'),
	    content: $.i18n('message-delete-search-confirm'),
	    buttons: {
	        confirm: {
	            text: $.i18n('label-delete'),
	            btnClass: 'btn-danger',
	            keys: ['enter'],
	            action: function(){
	            	searchListService.removeSearch(searchId)
	            }
	        },
	        cancel: {
	            text: $.i18n('label-cancel'),
	            btnClass: 'btn',
	            keys: ['esc']
	        },
	    }
	});
});