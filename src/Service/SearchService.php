<?php

namespace App\Service;

use App\Entity\Search;
use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SearchService implements SearchServiceInterface
{
	const NEW_SEARCH_MAX_ITEMS_COUNT = 1000;

	private $allegro;

	public function __construct(AllegroServiceInterface $allegro)
	{
		$this->allegro = $allegro;
	}

	public function denormalizeSearch(?array $searchData): Search
    {
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer(array($normalizer, new ArrayDenormalizer()));

        return $serializer->denormalize($searchData, Search::class);
    }

    public function validateSearch(Search $search)
    {
    	try {
    		$itemCount = $this->allegro->getItemCount($search->getFiltersForApiRequest());
    	} catch (\SoapFault $e) {
    		return $e->getMessage();
    	}

    	if ($itemCount > self::NEW_SEARCH_MAX_ITEMS_COUNT) {
    		return sprintf(
    			"Znaleziono zbyt dużo przedmiotów (%d, podczas gdy limit wynosi %d). Spróbuj zawęzić kryteria wyszukiwania.",
    			$itemCount,
    			self::NEW_SEARCH_MAX_ITEMS_COUNT
    		);
    	}

    	return true;
    }






	//pasowaloby searcha z initial serczem puszczac jakos w jednej transakcji chyba, ew. aktywowac jakos searcha dopiero po wgraniu initial data

	/*public function getSearchFromData(string $name, array $filters): Search
	{


		return new Search(
			$name,
			$filtersCollection
		);
	}*/
}
