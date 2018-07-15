class FilterService
{
	constructor(filterCollection, displayService)
	{
		this._filterCollection = filterCollection;
		this._displayService = displayService;
	}

	updateMeta()
	{
		var currentFilters = this._filterCollection.getFiltersForApi();

		//console.log(currentFilters);

		//console.log(currentFilters);

		$.getJSON('/ajax/search/filters', {"currentFilters": currentFilters}, $.proxy(function(filters) {
			// Save received filters
			this._filterCollection.setMeta(filters.available);

			console.log(this._filterCollection);

			// Update displayed filters picker (so it will show updated filters)
			this._displayService.updateFiltersPicker();



			
			this._displayService.displayFilters();

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





		/*
		//ugly af, should be rewritten

		var empty = true;
		var valid = true;
		$.each(values, function(i, value){
			if (value !== '') {
				empty = false; 
			} else {
				return;
			}

			if (!valid) {
				return false;
			}

			//
			if (meta.filterDataType === 'long' || meta.filterDataType === 'int') {
				valid = validator.isInt(value, {allow_leading_zeroes: false});
			} else if (meta.filterDataType === 'float') {
				if (meta.filterId === 'price') {
					valid = (validator.isDecimal(value, {locale: 'pl-PL', decimal_digits: '0,2'}) && validator.isFloat(value, {locale: 'pl-PL', min: 0}));
				} else {
					valid = validator.isDecimal(value, {locale: 'pl-PL'});
				}
			}
		});

		return (!empty && valid);*/

		return true;
	}
}