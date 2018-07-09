<?php

namespace App\Service;

use App\Entity\Search;

class ItemService implements ItemServiceInterface
{
    //Collection or just an array
    public function setStatus($items, int $status): void
    {
        foreach ($items as $item) {
            $item->setStatus($status);
        }
    }

    //Collection or just an array
    public function setSearch($items, Search $search): void
    {
        foreach ($items as $item) {
            $item->setSearch($search);
        }
    }
}
