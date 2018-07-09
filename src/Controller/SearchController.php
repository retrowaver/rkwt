<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    /**
     * @Route("/search/new", name="search_new")
     */
    public function newSearch(AllegroServiceInterface $allegro)
    {
        //print_r($allegro->getItems([]));


        return $this->render('search/search_new.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    /**
     * @Route("/search/list", name="search_list")
     */
    public function searchList()
    {
        //dump($this->getUser()->getSearches()->first()->getFilters()->first()->getFilterValues()->first());

        return $this->render('search/search_list.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
