<?php

namespace App\Service;

use App\Entity\Search;
use App\Entity\User;
use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;

class SearchService implements SearchServiceInterface
{
    const NEW_SEARCH_MAX_ITEMS_COUNT = 2000;
    const SEARCH_NAME_MAX_LENGTH = 40;
    const FILTER_VALUE_MAX_LENGTH = 50;

    // localized in js
    const ERROR_DEFAULT = 'error-search-default';
    const ERROR_NO_FILTERS = 'error-search-no-filters';
    const ERROR_TOO_MANY_ITEMS = 'error-search-too-many-items';
    const ERROR_SEARCH_NAME_TOO_LONG = 'error-search-search-name-too-long';

    private $em;
    private $allegro;
    private $translator;
    private $session;

	public function __construct(
        EntityManagerInterface $em,
        AllegroServiceInterface $allegro,
        TranslatorInterface $translator,
        SessionInterface $session
    ) {
        $this->em = $em;
		$this->allegro = $allegro;
        $this->translator = $translator;
        $this->session = $session;
	}

    public function saveNewSearch(User $user, array $searchData)
    {
        // Denormalize and sanitize search data
        $search = $this->denormalizeSearch($searchData);
        $this->sanitizeSearch($search);

        // Validate search and return array with error message if validation fails
        $errorMessage = $this->validateSearch($search);
        if (is_array($errorMessage)) {
            return $errorMessage;
        }
        
        // Everything's OK, so let's persist the search object
        $search->setUser($user);
        $search->setStatus(0);

        $this->em->persist($search);
        $this->em->flush();

        // Add flash message
        $this->session->getFlashBag()->add(
            'notice',
            $this->translator->trans('message.new-search-added')
        );

        return true;
    }

    public function saveEditedSearch(User $user, Search $search, array $searchData)
    {
        // Denormalize and sanitize search data, creating a temporary entity
        $editedSearch = $this->denormalizeSearch($searchData);
        $this->sanitizeSearch($editedSearch);

        // Pass data from temporary entity to entity from db
        $search->setName($editedSearch->getName());
        $search->setFilters($editedSearch->getFilters());

        // Validate search and return array with error message if validation fails
        $errorMessage = $this->validateSearch($search);
        if (is_array($errorMessage)) {
            return $errorMessage;
        }

        // Everything's OK, so let's persist the search object
        $search->setStatus(0);
        $search->setTimeLastSearched(null);
        $search->setTimeLastFullySearched(null);

        $this->em->flush();

        // Add flash message
        $this->session->getFlashBag()->add(
            'notice',
            $this->translator->trans('message.edited-search-saved')
        );

        return true;
    }

    private function denormalizeSearch(?array $searchData): Search
    {
        $searchData = $this->preDenormalizeSearch($searchData);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());
        $serializer = new Serializer(array($normalizer, new ArrayDenormalizer()));

        return $serializer->denormalize($searchData, Search::class, null, ['groups' => ['search_save']]);
    }

    private function sanitizeSearch(Search $search): Search
    {
        $search->setName(trim($search->getName()));

        return $search;
    }

    private function validateSearch(Search $search)
    {
        // Check the length of search name
        if (strlen($search->getName()) < 0 || strlen($search->getName()) > 40) {
            return [self::ERROR_DEFAULT];
        }

        // Check if search has at least one filter
        if ($search->getFiltersCount() === 0) {
            return [self::ERROR_NO_FILTERS];
        }

        // Check search against Allegro API
        //
        // If API error code is known (i.e. ERR_INCORRECT_FILTER_VALUE), then
        // pass it directly to user. If it's not, then display a default error.
        try {
            $filtersInfo = $this->allegro->getFiltersInfo($search->getFiltersForApi());
        } catch (\SoapFault $e) {
            switch ($e->faultcode) {
                case 'ERR_INCORRECT_FILTER_VALUE':
                    return $e->getMessage();
                    break;
                default:
                     return [self::ERROR_DEFAULT];
            }
        }

        // Check if any filters were rejected by Allegro API
        // (this should only happen due to user modifying the request)
        // https://allegro.pl/webapi/documentation.php/show/id,1342#method-output
        if (!empty($filtersInfo['rejected'])) {
            return [self::ERROR_DEFAULT];
        }

        // Check the amount of items found
        if ($filtersInfo['itemsCount'] > self::NEW_SEARCH_MAX_ITEMS_COUNT) {
            return [
                self::ERROR_TOO_MANY_ITEMS,
                $filtersInfo['itemsCount'],
                self::NEW_SEARCH_MAX_ITEMS_COUNT
            ];
        }

        // Check whether any filters are duplicated
        // (this should only happen due to user modifying the request)
        if ($this->hasSearchDuplicatedFilterIds($search)) {
            return [self::ERROR_DEFAULT];
        }

        // Check whether there are any filters with unexpected ids
        // (this case should be ruled out by "rejected" check above, but better safe than sorry)
        if ($this->hasSearchUnexpectedFilterIds($search, $filtersInfo['available'])) {
            return [self::ERROR_DEFAULT];
        }

        // Check if search contains unwanted filters
        if ($this->hasSearchBannedFilterIds($search)) {
            return [self::ERROR_DEFAULT];
        }
        
        // Check if search name is too long
        if (strlen($search->getName()) > self::SEARCH_NAME_MAX_LENGTH) {
            return [self::ERROR_SEARCH_NAME_TOO_LONG];
        }

        // Check if filter values do not exceed max allowed length
        foreach ($search->getFilters() as $filter) {
            foreach ($filter->getRawValues() as $value) {
                if (strlen($value) > self::FILTER_VALUE_MAX_LENGTH) {
                    return [self::ERROR_DEFAULT];
                }
            }
        }

        return true;
    }

    private function hasSearchUnexpectedFilterIds(Search $search, array $availableFiltersFromAllegro): bool
    {
        $filterIds = $search->getFiltersIds();

        $filterIdsFromAllegro = array_map(
            function($f) {
                return $f->filterId;
            },
            $availableFiltersFromAllegro
        );

        return (count(array_diff($filterIds, $filterIdsFromAllegro)) > 0);
    }

    private function hasSearchDuplicatedFilterIds(Search $search): bool
    {
        $filterIds = $search->getFiltersIds();

        return (count($filterIds) !== count(array_unique($filterIds)));
    }

    private function hasSearchBannedFilterIds(Search $search): bool
    {
        $filterIds = $search->getFiltersIds();
        $bannedIds = $this->allegro->getBannedFilterIds();

        return (count(array_diff($bannedIds, $filterIds)) < count($bannedIds));
    }

    private function preDenormalizeSearch(array $searchData): array
    {
        if (empty($searchData['filters'])) {
            return $searchData;
        }

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
}
