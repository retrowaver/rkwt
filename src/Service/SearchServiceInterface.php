<?php

namespace App\Service;

use App\Entity\Search;
use App\Entity\User;

interface SearchServiceInterface
{
	public function saveNewSearch(User $user, array $searchData);

	public function saveEditedSearch(User $user, Search $search, array $searchData);
}
