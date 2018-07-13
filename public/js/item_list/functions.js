const displayService = new DisplayService();
const itemService = new ItemService(displayService);

$(".remove-item").click(function() {
	var itemId = $(this).data("item-id");
	itemService.removeItem(itemId);
});