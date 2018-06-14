<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchAjaxController extends AbstractController
{
    /**
     * @Route("/ajax/search/filters", name="get_available_filters")
     */
    public function getAvailableFilters(Request $request, AllegroServiceInterface $allegro): JsonResponse
    {
    	$currentFilters = $request->query->get('currentFilters');

        return new JsonResponse($allegro->getFiltersInfo($currentFilters));
    }
}
