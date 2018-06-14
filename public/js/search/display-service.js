class DisplayService
{
	constructor(filterCollection, templates)
	{
		this._filterCollection = filterCollection;
		this._templates = templates;
	}

	displayModal(filterId)
	{
		var filter = this._filterCollection.getMetaByIds([filterId])[0];

		var modalTitle = filter.filterName;
		var modalBody = this._getModalBody(filter);

		$("#new-search-modal").find(".modal-title").html(modalTitle);
		$("#new-search-modal").find(".modal-body").html(modalBody);
		$("#new-search-modal").find(".add-new-filter").data('filterId', filter.filterId);

		$("#new-search-modal").modal('show');
	}

	updateFiltersPicker()
	{
		var select = this._templates.searchSelect({
			'basicFilters': this._filterCollection.getMetaByIds(['search', 'category', 'userId']),
			'countryFilters': this._filterCollection.getMetaByParameter('customCategory', 'country'),
			'categoryFilters': this._filterCollection.getMetaByParameter('filterType', 'category'),
		});

		$("#filters-container").html(select);
	}

	addFilter(filterId)
	{
		var filter = this._templates.filterDisplay({
			'meta': this._filterCollection.getMetaByIds([filterId])[0],
			'values': this._filterCollection.getValues(filterId),
		});
		$("#filters").append(filter);
	}

	removeFilter(filterId)
	{
		$("#filters").find('#filter-row-' + filterId).remove();
	}

	_getModalBody(filter)
	{
		if (filter.filterControlType === 'combobox') {
			return this._templates.newSearchCombobox(filter);
		}

		if (filter.filterControlType === 'checkbox') {
			return this._templates.newSearchCheckbox(filter);
		}

		if (filter.filterControlType === 'textbox') {
			return this._templates.newSearchTextbox(filter);
		}
	}
}