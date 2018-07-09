class FilterCollection
{
	constructor()
	{
		this._meta = {};
		this._values = {};
	}

	getFiltersForApiRequest()
	{
		var filtersForApiRequest = [];

		$.each(this._values, $.proxy(function(filterId, values){
			var meta = this.getMetaByParameter('filterId', filterId)[0];
			//console.log(meta);

			var filter = {'filterId': filterId};

			if (!meta.filterIsRange) {
				filter.filterValueId = values;
			} else {
				filter.filterValueRange = {};
				if (values[0] !== '') {
					filter.filterValueRange.rangeValueMin = values[0];
				}

				if (values[1] !== '') {
					filter.filterValueRange.rangeValueMin = values[1];
				}
			}

			filtersForApiRequest.push(filter);
		}, this));

		return filtersForApiRequest;
		//console.log(filtersForApiRequest);
	}





	getFiltersForPhp()
	{
		var filtersForApiRequest = [];

		$.each(this._values, $.proxy(function(filterId, values){
			var meta = this.getMetaByParameter('filterId', filterId)[0];
			//console.log(meta);

			var filter = {'filterId': filterId};

			if (!meta.filterIsRange) {
				filter.filterValues = [];
				$.each(values, function(key, value){
					filter.filterValues.push(
						{'filterValue': value}
					);
				});

			} else {
				if (values[0] !== '') {
					filter.valueRangeMin = values[0];
				}

				if (values[1] !== '') {
					filter.valueRangeMax = values[1];
				}
			}

			filtersForApiRequest.push(filter);
		}, this));

		return filtersForApiRequest;
		//console.log(filtersForApiRequest);
	}











	addValues(filterId, values)
	{
		this._values[filterId] = values;
	}

	setMeta(filters)
	{
		////////////////////////////////////////////////////////////////this._meta = {};////////////////////////DEVELOPING
		$.each(filters, $.proxy(function(i, filter){
			this._meta[filter.filterId] = filter;
		}, this));
	}

	/*getMeta()
	{
		return this._meta;
	}*/

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

	getMetaByIds(ids)
	{
		//idsy zachowuja kolejnosc
		var meta = [];

		//console.log(ids);
		//console.log(this._meta);

		$.each(this._meta, function(i, filter){
			var index = ids.indexOf(filter.filterId);
			if (index !== -1) {
				meta[index] = filter;
			}
		});

		return meta;
	}

	getValues(filterId)
	{
		return this._values[filterId];
	}

	removeValues(filterId)
	{
		delete this._values[filterId];
	}
}