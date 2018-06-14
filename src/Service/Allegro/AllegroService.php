<?php

namespace App\Service\Allegro;

use App\Entity\Item;

class AllegroService implements AllegroServiceInterface
{
	const API_URL = 'https://webapi.allegro.pl/service.php?wsdl';
	const COUNTRY_CODE = 1; // 1 for Poland
	const GET_ITEMS_BATCH_SIZE = 50; // 1-1000

	const BASIC_FILTERS = ['search', 'category', 'userId'];
	const COUNTRY_FILTERS = ['price', 'condition', 'offerType', 'shippingTime', 'offerOptions'];

	private $apiKey;
	private $soap;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;

		$this->soap = $this->getSoapClient();
	}

	public function getItems(array $filters): array
	{
		$request = [
			'webapiKey' => $this->apiKey,
			'countryId' => self::COUNTRY_CODE,
			'filterOptions' => $this->convertFiltersToRequest($filters),
			//'resultScope' => 3, // don't return filters and categories, just items
			'resultSize' => self::GET_ITEMS_BATCH_SIZE,
		];

		$offset = 0;
		$itemsList = [];
		do {
			$request['resultOffset'] = $offset++ * self::GET_ITEMS_BATCH_SIZE;
			$result = $this->soap->doGetItemsList($request);

			//print_r($result);


			$itemsList = array_merge($itemsList, $result->itemsList->item ?? []);
		} while (isset($result->itemsList->item));

		return $this->convertItemsListToItems($itemsList);
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

		$result = $this->soap->doGetItemsList($request);

		// Process available filters
		$available = $result->filtersList->item;
		$available = $this->filterFilters($available);
		$available = $this->categorizeFilters($available);

		//
		$filters = [
			'available' => $available,
			'rejected' => $result->filtersRejected->item ?? [],
			'itemsCount' => $result->itemsCount,
		];

		return $filters;
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

	private function convertFiltersToRequest(array $filters): array
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
	}

	private function convertItemsListToItems(array $itemsList): array
	{
		$items = [];
		foreach ($itemsList as $row) {
			$items[] = new Item(
				$row->itemId,
				$row->itemTitle,
				$row->priceInfo->item[0]->priceValue,
				$row->photosInfo->item[0]->photoUrl ?? null
			);
		}
		return $items;
	}
}
