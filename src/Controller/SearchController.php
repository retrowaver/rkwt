<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Service\Allegro\AllegroServiceInterface;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     */
    public function index(AllegroServiceInterface $allegro)
    {
    	print_r($allegro->getItems([]));


        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
