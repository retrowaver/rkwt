<?php

namespace App\Service;

use App\Entity\Search;

interface ItemServiceInterface
{
	public function setStatus($items, int $status): void;
	public function setSearch($items, Search $search): void;
}
