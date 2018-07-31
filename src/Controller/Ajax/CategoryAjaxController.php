<?php

namespace App\Controller\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Repository\CategoryRepository;

class CategoryAjaxController extends AbstractController
{
    /**
     * @Route("/ajax/category/get/{categoryId}", requirements={"categoryId": "\d+"}, name="ajax_category_get", condition="request.isXmlHttpRequest()")
     */
    public function getCategories(Request $request, int $categoryId, CategoryRepository $categoryRepository): JsonResponse
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $categories = [
            'categories' => $categoryRepository->findByCatParent($categoryId),
            'parentCategory' => $categoryRepository->findByCatId($categoryId)[0] ?? null
        ];

        return new JsonResponse(
            $serializer->serialize($categories, 'json'), 200, [], true
        );
    }
}
