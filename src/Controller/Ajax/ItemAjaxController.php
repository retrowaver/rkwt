<?php

namespace App\Controller\Ajax;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Item;

class ItemAjaxController extends AbstractController
{
    /**
     * Ajax route for removing an item
     *
     * Note: it doesn't actually immediately remove an item from db - just
     * marks it with status 0. It means two things:
     * - that it won't be displayed to the user (on items list or wherever else)
     * - that it will get deleted on the next full search update (but only if
     * associated auction became inactive (finished / deleted / etc.) - otherwise
     * there would be no way of knowing whether a newly found auction is truly new,
     * or was found and deleted before)
     *
     * @Route("/ajax/item/remove/{id}", requirements={"id": "\d+"}, name="ajax_item_remove", condition="request.isXmlHttpRequest()")
     * @Security("user == item.getSearch().getUser()")
     */
    public function removeItem(Item $item): JsonResponse
    {
        $item->setStatus(0);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }
}
