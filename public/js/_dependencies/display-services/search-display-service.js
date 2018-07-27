class SearchDisplayService
{
	constructor(filterCollection, templates, dataContainer)
	{
		this._filterCollection = filterCollection;
		this._templates = templates;
		this._dataContainer = dataContainer;
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

		// If we are editing, then display previously saved values
		// (checked checkboxes, filled inputs, etc.).
		if (edit) {
			var values = this._filterCollection.getValuesForDisplay(meta.filterId);
			this._alterModalWithExistingValues(meta, values);

			// Assumes that correctness of data didn't change since last save.
			this.enableSaveButton();
		} else {
			this.disableSaveButton();
		}

		// Special stuff for special types of filters.
		if (meta.filterId === 'category') {
			this.updateCategoryPickerTree();
		}

		// Final step - display modal
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
		//console.log(this._filterCollection.meta);


		// disabling already used filters - should be rewritten somehow
		var currentFiltersIds = this._filterCollection.getFiltersIds();
		$.each(currentFiltersIds, function(i, filterId){
			$("#filters-container").find("option[value='" + filterId + "']").attr("disabled", true);
		});
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
		// Insert data into template.
		var filter = this._templates.filterDisplay({
			'meta': this._filterCollection.getMetaByIds([filterId])[0],
			'values': this._filterCollection.getValuesForDisplay(filterId),
			'descriptions': this._getDescription(filterId),
		});

		// Display (as new or replace an existing one).
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

	displayError(errorMessage)
	{
	    $.alert({
	        //title: 'Błąd',
	        title: $.i18n('label-error'),
	        content: errorMessage,
	    });
	}

	openChangeSearchNameInput(searchName)
	{
		$(".change-search-name-input").show().focus().val("").val(
			validator.unescape(searchName)
		);
		$(".search-name").hide();
		$(".change-search-name").hide();
	}

	// Hides stuff and replaces search name
	closeChangeSearchNameInput(newSearchName)
	{
		$(".search-name").html(
			validator.escape(newSearchName)
		);
		$(".search-name").show();
		$(".change-search-name").show();
		$(".change-search-name-input").hide();
	}

	updateCategoryPickerTree(categoryId = null)
	{
		if (categoryId === null) {
			categoryId = $("#category-id").val();
		}

		$.getJSON('/ajax/category/get/' + categoryId, {csrfToken: this._dataContainer.csrfToken}, $.proxy(function(data) {
			data.isCurrentCategoryTopLevel = (categoryId == 0);

			$("#category-picker-list").html(
				this._templates.newSearchCategoryPickerList(data)
			);

			if (data.parentCategory !== null) {
				this.enableSaveButton();
				$(".chosen-category-info").html(
					$.i18n('message-chosen-category', data.parentCategory.catName)
				);
			} else {
				this.disableSaveButton();
				$(".chosen-category-info").html();
			}
		}, this));
	}

	disableSaveButton()
	{
		$(".save-filter").attr("disabled", true);
	}

	enableSaveButton()
	{
		$(".save-filter").attr("disabled", false);
	}

	_getDescription(filterId)
	{
		var values = this._filterCollection.getValuesForDisplay(filterId);
		var meta = this._filterCollection.getMetaById(filterId);

		switch (meta.filterControlType) {
			case 'checkbox':
				return this._getDescriptionForCheckbox(values, meta);
				break;
			case 'combobox':
				return this._getDescriptionForCombobox(values, meta);
				break;
			case 'textbox':
				return this._getDescriptionForTextbox(values, meta);
				break;
		}
	}

	_getDescriptionForCheckbox(values, meta)
	{
		//console.log(values);
		//return ['Zaznaczono filtrów: ' + values.filterValueId.length];
		return ['...'];
	}

	_getDescriptionForCombobox(values, meta)
	{
		return ['...'];
	}

	_getDescriptionForTextbox(values, meta)
	{
		//console.log(values);
		var content = [];
		if (meta.filterIsRange) {
			if (values.filterValueRange.rangeValueMin) {
				//content.push('od ' + values.filterValueRange.rangeValueMin);
				content.push(
					$.i18n('label-min-value-x', values.filterValueRange.rangeValueMin)
				);
			}
			if (values.filterValueRange.rangeValueMax) {
				//content.push('do ' + values.filterValueRange.rangeValueMax);
				content.push(
					$.i18n('label-max-value-x', values.filterValueRange.rangeValueMax)
				);
			}
		} else {
			content.push(values.filterValueId[0]);
		}

		return content;
	}

	_getModalBody(meta)
	{
		// Special forms.
		switch (meta.filterId) {
			case 'category':
				return this._templates.newSearchCategory(meta);
				break;
			case 'userId':
				return this._templates.newSearchUserId(meta);
				break;
		}

		// Standard forms.
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
		// Special forms.
		switch (meta.filterId) {
			case 'category':
				this._alterCategoryModalWithExistingValues(meta, values);
				return true;
				break;
			case 'userId':
				this._alterUserIdModalWithExistingValues(meta, values);
				return true;
				break;
		}

		// Standard forms.
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

	_alterUserIdModalWithExistingValues(meta, values)
	{
		var userId = values.filterValueId[0];
		$("input[name='new-filter-value[]']").val(userId);

		// Username isn't stored in db, therefore has to be requested.
		$.getJSON('/ajax/allegro/username', {userId: userId, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(data) {
			$("#user-id-picker-username").val(data.username);
		}, this));
	}

	_alterCategoryModalWithExistingValues(meta, values)
	{
		$("input[name='new-filter-value[]']").val(values.filterValueId[0]);
	}

	_alterCheckboxModalWithExistingValues(meta, values)
	{
		$.each(values.filterValueId, function(){
			$("#filter-" + this).prop("checked", true);
		});
	}

	_alterComboboxModalWithExistingValues(meta, values)
	{
		$("select[name='new-filter-value[]']").val(values.filterValueId[0]);
	}

	_alterTextboxModalWithExistingValues(meta, values)
	{
		if (!meta.filterIsRange) {
			$("input[name='new-filter-value[]']").val(values.filterValueId[0]);
		} else {
			$("#start-value").val(values.filterValueRange.rangeValueMin);
			$("#end-value").val(values.filterValueRange.rangeValueMax);
		}
	}
}