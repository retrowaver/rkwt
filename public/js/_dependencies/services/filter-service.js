class FilterService
{
	constructor(filterCollection, displayService, dataContainer)
	{
		this._filterCollection = filterCollection;
		this._displayService = displayService;
		this._dataContainer = dataContainer;

		this._BASIC_FILTERS_IDS = ['category', 'userId', 'search'];
	}

	// Updates filters: 
	updateFilters(edit = false)
	{
		// Check if there's at least one basic filter present. If there isn't,
		// then remove all remaining filters (because all more sophisticated
		// filters need one of the basic filters - otherwise search will be
		// invalid)
		var currentIds = this._filterCollection.getFiltersIds();
		if (this._BASIC_FILTERS_IDS.filter(x => currentIds.indexOf(x) !== -1).length > 0) {
			var currentFilters = this._filterCollection.getFiltersForApi();
		} else {
			var currentFilters = [];
			$.each(currentIds, $.proxy(function(i, filterId){
				this.removeFilter(filterId);
			}, this));
		}

		// Request available filters (passing current filters - if there are any)
		// https://allegro.pl/webapi/tutorials.php/tutorial/id,281
		// https://allegro.pl/webapi/documentation.php/show/id,1342
		$.getJSON('/ajax/allegro/filters', {"currentFilters": currentFilters, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(filters) {
			// Save received meta information about filters (type, control type, etc.)
			this._filterCollection.setMeta(filters.available);

			// Update filters picker, so it will show updated filters (based on saved meta info)
			this._displayService.updateFiltersPicker();

			// Based on updated filters info, remove filters which aren't relevant anymore.
			//
			// Example: user had chosen 2 filters:
			// - category: PC parts -> CPUs
			// - amount of cores: 4
			// ... and then changed the category to just "PC parts", making the second filter irrevelant
			this._removeIncompatibleFilters();

			// If user is creating a new search, filters and their descriptions
			// are displayed once they're added. In case of editing an existing
			// search, they have to be displayed / generated all at once in the beginning
			if (edit) {
				this._displayService.displayFilters();
				//this._displayService.updateDescriptions();
			}
		}, this));
	}

	saveFilter(filterId, values)
	{
		// Add filter to the collection.
		this._filterCollection.addValues(filterId, values);

		// Add filter to display.
		this._displayService.addFilter(filterId);
	}

	removeFilter(filterId)
	{
		// Remove filter from the collection.
		this._filterCollection.removeValues(filterId);

		// Remove filter from display.
		this._displayService.removeFilter(filterId);
	}

	getUserIdByUsername(username) {
		$.getJSON('/ajax/allegro/userid', {username: username, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(data) {
			$("#user-id").val(data.userId);
			if (data.userId > 0) {
				displayService.enableSaveButton();
			} else {
				displayService.disableSaveButton();
			}
		}, this));
	}

	getValuesFromForm()
	{
		var formData = $('#new-search-form').serializeArray();
		var values = [];
		$.each(formData, function() {
			values.push(this.value);
		});

		return values;
	}

	validateFilter(filterId)
	{
		var meta = this._filterCollection.getMetaById(filterId);
		var values = filterService.getValuesFromForm();

		switch (meta.filterControlType) {
			case 'checkbox':
				return this._validateCheckboxFilter(values);
				break;
			case 'textbox':
				return this._validateTextboxFilter(values, meta);
				break;
		}
		
		return true;
	}

	_validateCheckboxFilter(values)
	{
		return (values.length > 0);
	}

	_validateTextboxFilter(values, meta)
	{
		// Check if 1 out of 1 or 2 out of 2 fields are empty
		if (values.join() === '') {
			return false;
		}

		// Check whether min value is larger than max value (for range filters with both fields filled)
		if (
			meta.filterIsRange
			&& (Number(values[0].replace(',', '.')) > Number(values[1].replace(',', '.')))
			&& values[0] !== ''
			&& values[1] !== ''
		) {
			return false;
		}

		// Do the same checks for both fields (or just one, if there's only one)
		for (var i = 0; i < 2; i++) {
			// Checks based on filterId
			// (exceptions made for most frequently used fields)
			switch (meta.filterId) {
				case 'price':
					return (
						validator.isDecimal(values[i], {locale: 'pl-PL', decimal_digits: '0,2'})
						&& validator.isFloat(values[i], {locale: 'pl-PL', min: 0})
					);
					break;
			}

			// Checks based on filterDataType
			switch (meta.filterDataType) {
				case 'long':
				case 'int':
					return validator.isInt(values[i], {allow_leading_zeroes: false});
					break;
				case 'float':
					return validator.isDecimal(values[i], {locale: 'pl-PL'});
					break;
			}
		}

		return true;
	}

	_removeIncompatibleFilters() {
		var valueIds = this._filterCollection.getFiltersIds();
		var metaIds = this._filterCollection.getMetaIds();

		// https://stackoverflow.com/a/33034768
		var diff = valueIds.filter(x => !metaIds.includes(x));
		$.each(diff, $.proxy(function(i, filterId){
			this.removeFilter(filterId);
		}, this));
	}
}