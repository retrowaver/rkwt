<?php

namespace App\Service;

use App\Entity\Search;
use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;




use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
// For annotations
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;





class SearchService implements SearchServiceInterface
{
	const NEW_SEARCH_MAX_ITEMS_COUNT = 2000;

	private $allegro;

	public function __construct(AllegroServiceInterface $allegro)
	{
		$this->allegro = $allegro;
	}

	public function denormalizeSearch(?array $searchData): Search
    {
        $searchData = $this->preDenormalizeSearch($searchData);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());
        $serializer = new Serializer(array($normalizer, new ArrayDenormalizer()));

        return $serializer->denormalize($searchData, Search::class, null, ['groups' => ['search_save']]);
    }

    public function validateSearch(Search $search)
    {
    	try {
    		$itemCount = $this->allegro->getItemCount($search->getFiltersForApi());
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

    private function preDenormalizeSearch(array $searchData): array
    {
        // Ugly as hell, but I have no idea how to do it in more elegant way
        $newFilters = [];
        foreach ($searchData['filters'] as $filter) {
            $newFilter = ['filterId' => $filter['filterId']];
            
            if (isset($filter['filterValueId'])) {
                $newFilter['filterValues'] = [];
                foreach ($filter['filterValueId'] as $filterValue) {
                    $newFilter['filterValues'][] = ['filterValue' => $filterValue];
                }
            } elseif (isset($filter['filterValueRange'])) {
                if (isset($filter['filterValueRange']['rangeValueMin'])) {
                    $newFilter['valueRangeMin'] = $filter['filterValueRange']['rangeValueMin'];
                }
                
                if (isset($filter['filterValueRange']['rangeValueMax'])) {
                    $newFilter['valueRangeMax'] = $filter['filterValueRange']['rangeValueMax'];
                }
            }
            
            $newFilters[] = $newFilter;
        }
        $searchData['filters'] = $newFilters;

        return $searchData;
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
