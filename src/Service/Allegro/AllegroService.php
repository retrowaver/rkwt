<?php

namespace App\Service\Allegro;

use App\Entity\Item;

class AllegroService implements AllegroServiceInterface
{
	const API_URL = 'https://webapi.allegro.pl/service.php?wsdl';
	const COUNTRY_CODE = 1; // 1 for Poland
	const GET_ITEMS_BATCH_SIZE = 50; // 1-1000

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
			'resultScope' => 3, // don't return filters and categories, just items
			'resultSize' => self::GET_ITEMS_BATCH_SIZE,
		];

		$offset = 0;
		$itemsList = [];
		do {
			$request['resultOffset'] = $offset++ * self::GET_ITEMS_BATCH_SIZE;
			$result = $this->soap->doGetItemsList($request);
			$itemsList = array_merge($itemsList, $result->itemsList->item ?? []);
		} while (isset($result->itemsList->item));

		return $this->convertItemsListToItems($itemsList);
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
