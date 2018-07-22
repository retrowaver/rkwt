<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\ItemRepository;



///////////////test
use App\Service\SearchUpdateService;
use App\Service\SearchUpdateServiceInterface;
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

    /**
     * @Route("/item/test2", name="item_test2")
     */
    public function test2(AllegroServiceInterface $allegro)
    {
        $categories = $allegro->getCategories();

        $entityManager = $this->getDoctrine()->getManager();

        $batchSize = 50;
        $i = 0;
        foreach ($categories as $category) {
            $entityManager->persist($category);

            $i++;
            if ($i % $batchSize === 0) {
                $entityManager->flush();
                $entityManager->clear();
            }
        }

        $entityManager->flush();
        $entityManager->clear();
        
        exit;
    }
}
