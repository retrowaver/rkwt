<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;

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
}
