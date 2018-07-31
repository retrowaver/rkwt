<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\ItemRepository;



///////////////test
use App\Service\Tools\SearchUpdateService;
use App\Service\Tools\SearchUpdateServiceInterface;
/////////

//////tylko robocze

use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;

/////


use Symfony\Component\Translation\TranslatorInterface;

class ItemController extends AbstractController
{
    /**
     * @Route({"pl": "/przedmioty/lista"}, defaults={"page": "1"}, name="item_list")
     * @Route({"pl": "/przedmioty/lista/{page}"}, requirements={"page": "[1-9]\d*"}, name="item_list_paginated")
     */
    public function itemList(int $page, ItemRepository $itemRepository, TranslatorInterface $translator)
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
