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
