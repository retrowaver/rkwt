$(".remove-item").click(function() {
	var itemId = $(this).data("item-id");
	itemService.removeItem(itemId);
});