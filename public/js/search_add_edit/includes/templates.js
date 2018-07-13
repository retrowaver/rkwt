class Templates
{
	constructor()
	{
		this.searchSelect = Handlebars.compile($("#search-select-template").html());
		this.newSearchCombobox = Handlebars.compile($("#new-search-combobox-template").html());
		this.newSearchCheckbox = Handlebars.compile($("#new-search-checkbox-template").html());
		this.newSearchTextbox = Handlebars.compile($("#new-search-textbox-template").html());
		//this.newSearchTextboxDatetime = Handlebars.compile($("#new-search-textbox-datetime-template").html());

		this.filterDisplay = Handlebars.compile($("#filter-display-template").html());
	}
}