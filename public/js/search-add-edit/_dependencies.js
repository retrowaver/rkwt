const templates = new TemplatesContainer();
const filterCollection = new FilterCollection(dataContainer);
const displayService = new SearchDisplayService(filterCollection, templates, dataContainer);
const filterService = new FilterService(filterCollection, displayService, dataContainer);
const searchService = new SearchService(filterCollection, filterService, displayService, dataContainer);