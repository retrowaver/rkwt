class DisplayService
{
	constructor(filterCollection, templates)
	{
		this._filterCollection = filterCollection;
		this._templates = templates;
	}

	displayModal(filterId, edit = false)
	{
		filterId = String(filterId);

		var meta = this._filterCollection.getMetaByIds([filterId])[0];

		var modalTitle = meta.filterName;
		var modalBody = this._getModalBody(meta);

		$("#new-search-modal").find(".modal-title").html(modalTitle);
		$("#new-search-modal").find(".modal-body").html(modalBody);
		$("#new-search-modal").find(".save-filter").data('filterId', meta.filterId); // button

		//
		if (edit) {
			var values = this._filterCollection.getValues(meta.filterId);
			//console.log(values);
			this._alterModalWithExistingValues(meta, values);
		}

		//special stuff for special types of filters
		if (meta.filterId === 'category') {
			this.updateCategoryPickerTree();
		}



		//
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

	displayFilters()
	{
		var filterIds = this._filterCollection.getFiltersIds();
		$.each(filterIds, $.proxy(function(i, filterId){
			this.addFilter(filterId);
		}, this));
	}

	addFilter(filterId)
	{
		var filter = this._templates.filterDisplay({
			'meta': this._filterCollection.getMetaByIds([filterId])[0],
			'values': this._filterCollection.getValues(filterId),
		});

		//console.log(filterId);

		if (!$("#filters").find('#filter-row-' + filterId).length) {
			$("#filters").append(filter);
		} else {
			$("#filters").find('#filter-row-' + filterId).replaceWith(filter);
		}
	}

	removeFilter(filterId)
	{
		$("#filters").find('#filter-row-' + filterId).remove();
	}

	updateCategoryPickerTree(categoryId = null)
	{
		if (categoryId === null) {
			categoryId = $("#category-id").val();
		}

		$.getJSON('/ajax/category/get/' + categoryId, {}, $.proxy(function(data) {
			data.isCurrentCategoryTopLevel = (categoryId == 0);

			$("#category-picker-list").html(
				this._templates.newSearchCategoryPickerList(data)
			);
		}, this));
	}

	_getModalBody(meta)
	{
		// Special forms
		switch (meta.filterId) {
			case 'category':
				return this._templates.newSearchCategory(meta);
				break;
		}

		// Standard forms
		switch (meta.filterControlType) {
			case 'combobox':
				return this._templates.newSearchCombobox(meta);
				break;
			case 'checkbox':
				return this._templates.newSearchCheckbox(meta);
				break;
			case 'textbox':
				return this._templates.newSearchTextbox(meta);
				break;
		}
	}

	_alterModalWithExistingValues(meta, values)
	{
		// Special forms
		switch (meta.filterId) {
			case 'category':
				this._alterCategoryModalWithExistingValues(meta, values);
				return true;
				break;
		}

		// Standard forms
		switch (meta.filterControlType) {
			case 'checkbox':
				this._alterCheckboxModalWithExistingValues(meta, values);
				break;
			case 'combobox':
				this._alterComboboxModalWithExistingValues(meta, values);
				break;
			case 'textbox':
				this._alterTextboxModalWithExistingValues(meta, values);
				break;
		}
	}

	_alterCategoryModalWithExistingValues(meta, values) {
		$("input[name='new-filter-value[]']").val(values.filterValueId[0]);
	}

	_alterCheckboxModalWithExistingValues(meta, values) {
		$.each(values.filterValueId, function(){
			$("#filter-" + this).prop("checked", true);
		});
	}

	_alterComboboxModalWithExistingValues(meta, values) {
		$("select[name='new-filter-value[]']").val(values.filterValueId[0]);
	}

	_alterTextboxModalWithExistingValues(meta, values) {
		if (!meta.filterIsRange) {
			$("input[name='new-filter-value[]']").val(values.filterValueId[0]);
		} else {
			$("#start-value").val(values.filterValueRange.rangeValueMin);
			$("#end-value").val(values.filterValueRange.rangeValueMax);
		}
	}
}