$( document ).ready(function() {

	const BASIC_FILTERS_IDS = ['search', 'category', 'userId'];
	const COUNTRY_FILTERS_IDS = ['price', 'condition', 'offerType', 'shippingTime', 'offerOptions'];

	var searchSelectTemplate = Handlebars.compile($("#search-select-template").html());
	var newSearchComboboxTemplate = Handlebars.compile($("#new-search-combobox-template").html());
	var newSearchCheckboxTemplate = Handlebars.compile($("#new-search-checkbox-template").html());
	var newSearchTextboxDefaultTemplate = Handlebars.compile($("#new-search-textbox-default-template").html());
	var newSearchTextboxDatetimeTemplate = Handlebars.compile($("#new-search-textbox-datetime-template").html());

	var currentFilters = {'currentFilters': [
		{'filterId': 'category', 'filterValueId': [89510]},
		//{'filterId': '7121', 'filterValueId': [1]},
		//{'filterId': '7122', 'filterValueId': [1]}
	]};

	var availableFilters = [];

	var filterNames = {};



	updateAvailableFilters(currentFilters);



	$(".add-new-filter").click(function() {
		var formData = $('#new-search-form').serializeArray();

		var values = [];
		var filterId;
		var filterIsRange;
		var filterName;

		$.each(formData, function() {
			//console.log(this.name);
			if (this.name === 'new-filter-value[]') {
				values.push(this.value);
			}

			if (this.name === 'filterId') {
				filterId = this.value;
			}

			if (this.name === 'filterName') {
				filterName = this.value;
			}

			if (this.name === 'filterIsRange') {
				filterIsRange = (this.value === 'true');
			}
		});

		//console.log(values);


		////////////////////////////////??DOPISAC SPRAWDZANIE, CZY FILTR O DANYM ID JUZ NIE ISTNIEJE
		/////////????ALSO DOPISAC SPRAWDZANIE, CZY WARTOSC / JEDNA Z WARTOSCI NIE JEST PUSTA



		// Add filter to filters
		addFilter(currentFilters, filterId, filterIsRange, values);
		//
		filterNames[filterId] = filterName;
		console.log(filterNames);

		// Refresh existing filters
		////////////////////
	});

	$(".new-filter").click(function() {

		// Get filter id from option value
		var filterId = $(this).prev().val();

		// Do nothing if default (placeholder) option was chosen
		if (!filterId) {
			alert('Najpierw wybierz rodzaj filtra'); //robocze
			return true;
		}

		//alert(filterId);
		//

		var filter = getFiltersByIds([filterId], availableFilters)[0];

		// Set modal title
		$("#new-search-modal").find(".modal-title").html(filter.filterName);

		//console.log(filter);

		// Set modal body
		var modalBody = '';
		if (filter.filterControlType === 'combobox') {
			modalBody = newSearchComboboxTemplate(filter);
		} else if (filter.filterControlType === 'checkbox') {
			modalBody = newSearchCheckboxTemplate(filter);
		} else if (filter.filterControlType === 'textbox') {
			if (filter.filterDataType === 'datetime') {
				modalBody = newSearchTextboxDatetimeTemplate(filter);
			} else {
				modalBody = newSearchTextboxDefaultTemplate(filter);
			}
		}

		$("#new-search-modal").find(".modal-body").html(modalBody);

		//
		$("#new-search-modal").modal('show');

	});

	function addFilter(currentFilters, filterId, filterIsRange, values)
	{
		var filter = {'filterId': filterId};

		if (!filterIsRange) {
			filter.filterValueId = values;
		} else {
			filter.filterValueRange = {}
			if (values[0] !== '') {
				filter.filterValueRange.rangeValueMin = values[0];
			}

			if (values[1] !== '') {
				filter.filterValueRange.rangeValueMin = values[1];
			}
		}

		// 
		currentFilters.currentFilters.push(filter);

		// Display new filter
		//console.log(currentFilters);
		updateDisplayedFilters(currentFilters);
	}

	////////function removeFilter()

	function updateDisplayedFilters(currentFilters)
	{
		//$("#filters-container").html('HEJOOOOOOOOOOO');
		$.each(currentFilters, function() {

		});
	}

	function updateAvailableFilters(currentFilters)
	{
		//console.log(BASIC_FILTERS);

		//loader start

		$.getJSON('/ajax/search/filters', currentFilters, function (filters) {

			//UPDATE SELECTS BASED ON RECEIVED FILTERS

			updateSelect('basic', getFiltersByIds(BASIC_FILTERS_IDS, filters.available));
			updateSelect('country', getFiltersByIds(COUNTRY_FILTERS_IDS, filters.available));
			updateSelect('category', getFiltersByType('category', filters.available));

			availableFilters = filters.available;

			//LOADER END
		});
	}

	function getFiltersByIds(ids, filters)
	{
		return filters.filter(x => ids.indexOf(x.filterId) !== -1);
	}

	function getFiltersByType(type, filters)
	{
		return filters.filter(x => x.filterType === type);
	}

	function updateSelect(id, filters)
	{
		var select = searchSelectTemplate({
			'id': id,
			'filters': filters,
		});
		$("#" + id + "-filters-container").html(select);
	}



	/*function updateAvailableFilters(availableFilters)
	{
		updateSelect('basic', availableFilters.basic);
		updateSelect('country', availableFilters.country);
		updateSelect('category', availableFilters.category);
	}

	function update

	function updateSelect(id)
	{

	}*/

	//console.log(availableFilters);


});