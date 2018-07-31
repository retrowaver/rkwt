<?php

namespace App\Service;

use App\Entity\Search;

interface ItemServiceInterface
{
    /**
     * Sets status of all items in array / collection
     *
     * @param array|Collection|Item[] $items
     * @param bool $status
     *
     * @return void
     */
	public function setStatus($items, int $status): void;
	
    /**
     * Sets search of all items in array / collection
     *
     * @param array|Collection|Item[] $items
     * @param Search $search
     *
     * @return void
     */
	public function setSearch($items, Search $search): void;
}
