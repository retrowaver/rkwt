class TemplatesContainer
{
	constructor(Handlebars)
	{
		Handlebars.registerHelper('trans',
			function(str){
				return $.i18n(str.fn(this));
			}
		);

		this.searchSelect = Handlebars.compile($("#search-select-template").html());
		this.newSearchCombobox = Handlebars.compile($("#new-search-combobox-template").html());
		this.newSearchCheckbox = Handlebars.compile($("#new-search-checkbox-template").html());
		this.newSearchTextbox = Handlebars.compile($("#new-search-textbox-template").html());

		this.newSearchCategory = Handlebars.compile($("#new-search-category-template").html());
		this.newSearchCategoryPickerList = Handlebars.compile($("#new-search-category-picker-list-template").html());

		this.newSearchUserId = Handlebars.compile($("#new-search-user-id-template").html());

		//this.newSearchTextboxDatetime = Handlebars.compile($("#new-search-textbox-datetime-template").html());

		this.filterDisplay = Handlebars.compile($("#filter-display-template").html());
	}
}