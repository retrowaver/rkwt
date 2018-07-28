const templates = new TemplatesContainer(Handlebars);
const filterCollection = new FilterCollection(dataContainer);
const displayService = new SearchDisplayService(filterCollection, templates, dataContainer, validator, preloader);
const filterService = new FilterService(filterCollection, displayService, dataContainer, validator, preloader);
const searchService = new SearchService(filterCollection, filterService, displayService, dataContainer, preloader);