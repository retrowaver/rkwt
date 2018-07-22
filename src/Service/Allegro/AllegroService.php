<?php

namespace App\Service\Allegro;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Item;
use App\Entity\Category;

//use App\Entity\Item;

class AllegroService implements AllegroServiceInterface
{
	const API_URL = 'https://webapi.allegro.pl/service.php?wsdl';
	const COUNTRY_CODE = 1; // 1 for Poland
	const GET_ITEMS_BATCH_SIZE = 1000; // 1-1000

	const BASIC_FILTERS = ['search', 'category', 'userId'];
	const COUNTRY_FILTERS = ['price', 'condition', 'offerType', 'shippingTime', 'offerOptions'];

	//const PUBLIC_EXCEPTION_CODES = ['ERR_INCORRECT_FILTER_VALUE'];

	private $apiKey;
	private $soap;
	private $result; // stores last result from API call

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;

		$this->soap = $this->getSoapClient();
	}

	public function getItems(array $filterOptions): Collection
	{
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'filterOptions' => $filterOptions,
			'resultScope' => 3, // don't return filters and categories, just items
			'resultSize' => self::GET_ITEMS_BATCH_SIZE,
		];

		$offset = 0;
		$itemsList = [];
		do {
			//echo "Offset: " . ($offset * self::GET_ITEMS_BATCH_SIZE) . "<br>\n";

			$request['resultOffset'] = $offset++ * self::GET_ITEMS_BATCH_SIZE;
			$this->result = $this->soap->doGetItemsList($request);

			$itemsList = array_merge($itemsList, $this->result->itemsList->item ?? []);
		} while (isset($this->result->itemsList->item) && count($this->result->itemsList->item) === self::GET_ITEMS_BATCH_SIZE);

		return $this->convertItemsListToItems($itemsList);
	}

	public function getUserId(string $username): int
	{
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'userLogin' => $username
		];

		try {
			$this->result = $this->soap->doGetUserID($request);
		} catch(\SoapFault $e) {
			return 0;
		}
		
		return $this->result->userId;
	}

	public function getUsername(int $userId): string
	{
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'userId' => $userId
		];

		try {
			$this->result = $this->soap->doGetUserLogin($request);
		} catch(\SoapFault $e) {
			return '';
		}

		return $this->result->userLogin;
	}

	public function getCategories(): Collection
	{
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
		];

		$this->result = $this->soap->doGetCatsData($request);

		$categories = new ArrayCollection;
		foreach ($this->result->catsList->item as $row) {
			$category = new Category;

			$category->setCatId($row->catId);
			$category->setCatName($row->catName);
			$category->setCatParent($row->catParent);
			$category->setCatPosition($row->catPosition);
			$category->setCatIsLeaf($row->catIsLeaf);

			$categories->add($category);
		}

		return $categories;
	}

	public function getFiltersInfo(?array $currentFilters = null): array
	{
		// Make request to API
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'resultScope' => 6, // don't return items and categories, just filters
			'filterOptions' => $currentFilters,
		];

		$this->result = $this->soap->doGetItemsList($request);

		// Process available filters
		$available = $this->result->filtersList->item;
		$available = $this->filterFilters($available);
		$available = $this->categorizeFilters($available);

		//
		$filters = [
			'available' => $available,
			'rejected' => $this->result->filtersRejected->item ?? [],
			'itemsCount' => $this->result->itemsCount,
		];

		return $filters;
	}





	public function getItemCount(array $filters): int
	{
		// Make request to API
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'resultScope' => 7, // don't return items and categories, just filters
			'filterOptions' => $filters,
		];

		$this->result = $this->soap->doGetItemsList($request);

		return (int)$this->result->itemsCount;
	}




	private function filterFilters(array $filters): array
	{
		foreach ($filters as $key => $filter) {
			if (
				$filter->filterType !== 'category'
				&& (!in_array($filter->filterId, self::BASIC_FILTERS)
				&& !in_array($filter->filterId, self::COUNTRY_FILTERS))
			) {
				unset($filters[$key]);
			}
		}

		return $filters;
	}

	private function categorizeFilters(array $filters): array
	{
		foreach ($filters as $filter) {
			if (in_array($filter->filterId, self::BASIC_FILTERS)) {
				$filter->customCategory = 'basic';
			} elseif (in_array($filter->filterId, self::COUNTRY_FILTERS)) {
				$filter->customCategory = 'country';
			} else {
				$filter->customCategory = 'category';
			}
		}

		return $filters;
	}

	private function getSoapClient(): \SoapClient
	{
		return new \SoapClient(
			self::API_URL,
			[
				'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
				'exceptions' => true,
				'keep_alive' => false,
			]
		);
	}

	/*private function convertFiltersToRequest(array $filters): array
	{
		return [
			[
				'filterId' => 'search',
				'filterValueId' => ['stephen king christine'],
			],
			[
				'filterId' => 'category',
				'filterValueId' => [76102],
			]
		];
	}*/

	private function convertItemsListToItems(array $itemsList): Collection
	{
		$items = new ArrayCollection;
		foreach ($itemsList as $row) {
			$items->add(
				new Item(
					$row->itemId,
					$row->itemTitle,
					$row->priceInfo->item[0]->priceValue,
					$row->photosInfo->item[0]->photoUrl ?? null
				)
			);
		}
		return $items;
	}
}
