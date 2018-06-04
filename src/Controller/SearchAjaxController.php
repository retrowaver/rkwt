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
    	$filters = $request->query->get('filters');
    	
    	$filters = array_merge(
    		$filters['basic'] ?? [],
    		$filters['country'] ?? [],
    		$filters['category'] ?? []
    	);

    	$availableFilters = $allegro->getAvailableFilters($filters);

        return new JsonResponse($availableFilters);
    }
}
