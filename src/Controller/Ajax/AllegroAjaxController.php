<?php

namespace App\Controller\Ajax;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AllegroAjaxController extends AbstractController
{
    /**
     * @Route("/ajax/allegro/filters", name="ajax_allegro_filters", condition="request.isXmlHttpRequest()")
     */
    public function getAvailableFilters(Request $request, AllegroServiceInterface $allegro): JsonResponse
    {
        $currentFilters = $request->query->get('currentFilters');

        return new JsonResponse($allegro->getFiltersInfo($currentFilters));
    }

    /**
     * @Route("/ajax/allegro/userid", name="ajax_allegro_user_id", condition="request.isXmlHttpRequest()")
     */
    public function getUserIdByUsername(Request $request, AllegroServiceInterface $allegro): JsonResponse
    {
        $username = $request->query->get('username');

        return new JsonResponse(['userId' => $allegro->getUserId($username)]);
    }

    /**
     * @Route("/ajax/allegro/username", name="ajax_allegro_username", condition="request.isXmlHttpRequest()")
     */
    public function getUsernameByUserId(Request $request, AllegroServiceInterface $allegro): JsonResponse
    {
        $userId = $request->query->get('userId');

        return new JsonResponse(['username' => $allegro->getUsername($userId)]);
    }
}
