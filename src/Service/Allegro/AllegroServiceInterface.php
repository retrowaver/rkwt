<?php

namespace App\Service\Allegro;

use Doctrine\Common\Collections\Collection;

interface AllegroServiceInterface
{
	/**
	 * Gets items that are currently live on Allegro
	 *
	 * Gets items using doGetItemsList method, passing a set of filters.
	 * Optional parameter `onlyRecent = true` adds an additional filter,
	 * that results in getting only those items, that were recently added.
	 *
	 * @param array $filterOptions Filters array directly passed to API (read more: https://allegro.pl/webapi/documentation.php/show/id,1342)
	 * @param bool $onlyRecent
	 *
	 * @return Collection|Item[]
	 */
	public function getItems(array $filters, bool $onlyRecent = false): Collection;

	public function getCategories(): Collection;

	public function getUserId(string $username): int;

	public function getUsername(int $userId): string;

	public function getFiltersInfo(?array $filters): array;

	public function getBannedFilterIds(): array;
}
