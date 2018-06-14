<?php

namespace App\Service\Allegro;

interface AllegroServiceInterface
{
	public function getItems(array $filters): array;

	public function getFiltersInfo(?array $filters): array;
}
