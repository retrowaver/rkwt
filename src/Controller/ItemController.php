<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ItemController extends AbstractController
{
    /**
     * @Route({"pl": "/przedmioty/lista"}, defaults={"page": "1"}, name="item_list")
     * @Route({"pl": "/przedmioty/lista/{page}"}, requirements={"page": "[1-9]\d*"}, name="item_list_paginated")
     */
    public function itemList(int $page, ItemRepository $itemRepository)
    {
    	$items = $itemRepository->findLatest(
            $this->getUser()->getId(),
            [1, 2],
            $page
        );

        return $this->render('item/item_list.html.twig', [
            'items' => $items,
            'searchCount' => $this->getUser()->getSearchCount()
        ]);
    }
}
