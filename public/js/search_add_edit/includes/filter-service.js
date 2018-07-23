class FilterService
{
	constructor(filterCollection, displayService, dataContainer)
	{
		this._filterCollection = filterCollection;
		this._displayService = displayService;
		this._dataContainer = dataContainer;
	}

	//not just meta
	updateMeta(edit = false)
	{
		var basicFilters = ['category', 'userId', 'search']; //this should be moved somewhere
		var currentIds = this._filterCollection.getFiltersIds();

		//
		if (basicFilters.filter(x => -1 !== currentIds.indexOf(x)).length) {
			var currentFilters = this._filterCollection.getFiltersForApi();
		} else {
			var currentFilters = [];
			$.each(currentIds, $.proxy(function(i, filterId){
				this.removeFilter(filterId);
			}, this));
		}

		$.getJSON('/ajax/allegro/filters', {"currentFilters": currentFilters, csrfToken: this._dataContainer.csrfToken}, $.proxy(function(filters) {
			// Save received filters
			this._filterCollection.setMeta(filters.available);

			// Update displayed filters picker (so it will show updated filters)
			this._displayService.updateFiltersPicker();

			this._removeIncompatibleFilters();


			if (edit) {
				this._displayService.displayFilters();
				this._displayService.updateDescriptions();
			}

			//LOADER END
		}, this));
	}

	saveFilter(filterId, values)
	{
		//add filter to collection

		//console.log(filterId);
		//console.log(values);

		this._filterCollection.addValues(filterId, values);

		//add filter to display

		this._displayService.addFilter(filterId);
	}

	removeFilter(filterId)
	{
		//remove filter from collection
		this._filterCollection.removeValues(filterId);


		//remove filter from display
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

		//console.log(meta);

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
		// Check if 1/1 or 2/2 fields are empty
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

		//
		for (var i = 0; i < 2; i++) {
			// Checks based on filterId
			// (exceptions made for most frequently used fields)
			switch (meta.filterId) {
				case 'price':
					return (validator.isDecimal(values[i], {locale: 'pl-PL', decimal_digits: '0,2'}) && validator.isFloat(values[i], {locale: 'pl-PL', min: 0}));
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