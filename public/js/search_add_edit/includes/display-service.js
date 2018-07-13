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
		//console.log([filterId]);

		var filter = this._filterCollection.getMetaByIds([filterId])[0];
		//console.log(filter);

		var modalTitle = filter.filterName;
		var modalBody = this._getModalBody(filter);

		$("#new-search-modal").find(".modal-title").html(modalTitle);
		$("#new-search-modal").find(".modal-body").html(modalBody);
		$("#new-search-modal").find(".save-filter").data('filterId', filter.filterId); // button

		//
		if (edit) {
			var values = this._filterCollection.getValues(filter.filterId);
			//console.log(values);
			this._alterModalWithExistingValues(filter, values);
		}

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

	_alterModalWithExistingValues(meta, values)
	{
		if (meta.filterControlType === 'checkbox') {
			$.each(values.filterValueId, function(){
				$("#filter-" + this).prop("checked", true);
			});
		} else if (meta.filterControlType === 'combobox') {
			$("select[name='new-filter-value[]']").val(values.filterValueId[0]);
		} else if (meta.filterControlType === 'textbox') {
			if (!meta.filterIsRange) {
				$("input[name='new-filter-value[]']").val(values.filterValueId[0]);
			} else {
				$("#start-value").val(values.filterValueRange.rangeValueMin);
				$("#end-value").val(values.filterValueRange.rangeValueMax);
			}
		}
	}
}