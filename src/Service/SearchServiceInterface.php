<?php

namespace App\Service;

use App\Entity\Search;

interface SearchServiceInterface
{
	public function denormalizeSearch(array $searchData): Search;

	public function validateSearch(Search $search);
}
