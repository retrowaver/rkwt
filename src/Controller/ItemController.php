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
     * @Route("/item/list", name="item_list")
     */
    public function itemList(ItemRepository $itemRepository)
    {
    	$items = $itemRepository->findByUserId($this->getUser()->getId());

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

        echo 'ee5';
        exit;
    }
}
