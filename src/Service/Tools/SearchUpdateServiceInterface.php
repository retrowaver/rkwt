<?php

namespace App\Service\Tools;

use App\Entity\Search;
use Doctrine\Common\Collections\Collection;

interface SearchUpdateServiceInterface
{
	public function updateSearches(Collection $searches): void;

	public function updateSearch(Search $search): void;

	public function getActiveSearches(): Collection;
}
