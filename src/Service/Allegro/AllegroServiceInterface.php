<?php

namespace App\Service\Allegro;

use Doctrine\Common\Collections\Collection;

interface AllegroServiceInterface
{
	public function getItems(array $filters): Collection;

	public function getCategories(): Collection;

	public function getUserId(string $username): int;

	public function getUsername(int $userId): string;

	public function getFiltersInfo(?array $filters): array;
}
