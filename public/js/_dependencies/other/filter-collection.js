class FilterCollection
{
	constructor(dataContainer)
	{
		this._dataContainer = dataContainer;

		this._meta = {};
		this._values = {};
	}

	// Exports saved filter values in Allegro WebAPI compatible format.
	getFiltersForApi()
	{
		var filters = [];
		$.each(this._values, function(i, filter){
			var filterForApi = Object.assign({}, filter);
			filterForApi.filterId = i;
			filters.push(filterForApi);
		});

		return filters;
	}

	// Add values to the collection.
	addValues(filterId, values)
	{
		var meta = this.getMetaById(filterId);

		//
		if (this._dataContainer.locale === 'pl') {
			if (meta.filterDataType === 'float') {
				values = $.extend(true, {}, values);
				this._replace(",", ".", values);
			}
		}

		// Mapping
		var current = {};
		if (!meta.filterIsRange) {
			current.filterValueId = values;
		} else {
			current.filterValueRange = {};
			if (values[0] !== '') {
				current.filterValueRange.rangeValueMin = values[0];
			}

			if (values[1] !== '') {
				current.filterValueRange.rangeValueMax = values[1];
			}
		}

		this._values[filterId] = current;
	}

	setMeta(filters)
	{
		this._meta = {};
		$.each(filters, $.proxy(function(i, filter){
			this._meta[filter.filterId] = filter;
		}, this));
	}

	setValues(filters)
	{
		this._values = {};
		$.each(filters, $.proxy(function(i, filter){
			this._values[filter.filterId] = filter;
		}, this));
	}

	getMetaByParameter(parameter, value)
	{
		var meta = [];

		$.each(this._meta, function(i, filter){
			if (filter[parameter] === value) {
				meta.push(filter);
			}
		});

		return meta;
	}

	getMetaById(id)
	{
		return this._meta[id];
	}

	getMetaByIds(ids)
	{
		// Order of returned meta info matches the order of provided ids.
		var meta = [];

		$.each(this._meta, function(i, filter){
			var index = ids.indexOf(filter.filterId);
			if (index !== -1) {
				meta[index] = filter;
			}
		});

		return meta;
	}

	getFiltersIds()
	{
		return Object.keys(this._values);
	}

	getMetaIds()
	{
		return Object.keys(this._meta);
	}

	getValuesForDisplay(filterId)
	{
		var meta = this._meta[filterId];
		var values = this._values[filterId];

		if (this._dataContainer.locale === 'pl') {
			if (meta.filterDataType === 'float') {
				values = $.extend(true, {}, this._values[filterId]);
				this._replace("\\.", ",", values);
			}
		}
		
		return values;
	}

	removeValues(filterId)
	{
		delete this._values[filterId];
	}

	_replace(search, replace, obj)
	{
		// this should be rewritten
		search = new RegExp(search);
		$.each(obj, function(i, value){
			if (typeof(value) === 'string') {
				obj[i] = value.replace(search, replace);
			} else if (typeof(value) === 'object' || Array.isArray(value)) {
				$.each(value, function(j, value2){
					if (typeof(value2) === 'string') {
						obj[i][j] = value2.replace(search, replace);
					}
				});
			}
		});
	}
}