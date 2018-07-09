<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use App\Service\SearchServiceInterface;
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

    /**
     * @Route("/ajax/search/save", name="save_search")
     */
    public function saveSearch(Request $request, SearchServiceInterface $searchService): JsonResponse
    {
        $search = $searchService->denormalizeSearch($request->query->get('search'));
        $error = $searchService->validateSearch($search);

        if (!is_string($error)) {
            //persist

            //echo 'yeah';
            //exit;

            //echo $this->getUser()->

            //dump($this->getUser());
            //exit;

            $search->setUser($this->getUser());
            $search->setStatus(666); //

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($search);
            $entityManager->flush();

        }

        return new JsonResponse([
            'success' => ($error === true),
            'error' => $error !== true ? $error : null,
        ]);

    }


}
