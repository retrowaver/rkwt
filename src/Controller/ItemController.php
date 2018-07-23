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

class ItemController extends AbstractController
{
    /**
     * @Route("/item/list", defaults={"page": "1"}, name="item_list")
     * @Route("/item/list/{page}", requirements={"page": "[1-9]\d*"}, name="item_list_paginated")
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
        ]);
    }

    /**
     * @Route("/item/test", name="item_test")
     */
    public function test(SearchUpdateService $update)
    {
        $searches = $update->getActiveSearches();
        $update->updateSearches($searches);

        //echo 'ee5';
        //exit;
        return $this->render('item/item_list.html.twig', [
            'items' => [],
        ]);
    }
}
