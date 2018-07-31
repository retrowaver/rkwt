<?php

namespace App\Service;

use App\Entity\Search;

class ItemService implements ItemServiceInterface
{
    public function setStatus($items, int $status): void
    {
        foreach ($items as $item) {
            $item->setStatus($status);
        }
    }

    public function setSearch($items, Search $search): void
    {
        foreach ($items as $item) {
            $item->setSearch($search);
        }
    }
}
