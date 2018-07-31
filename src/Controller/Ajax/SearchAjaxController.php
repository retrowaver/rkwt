<?php

namespace App\Controller\Ajax;

use App\Entity\Search;
use App\Service\SearchServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;

class SearchAjaxController extends AbstractController
{
    /**
     * @Route("/ajax/search/save", name="ajax_search_save", condition="request.isXmlHttpRequest()")
     */
    public function saveSearch(Request $request, SearchServiceInterface $searchService): JsonResponse
    {
        $errorMessage = $searchService->saveNewSearch(
            $this->getUser(),
            $request->query->get('search')
        );

        return new JsonResponse([
            'success' => ($errorMessage === true),
            'error' => $errorMessage !== true ? $errorMessage : null,
        ]);
    }

    /**
     * @Route("/ajax/search/edit/{id}", requirements={"id": "\d+"}, name="ajax_search_edit", condition="request.isXmlHttpRequest()")
     * @Security("user == search.getUser()")
     */
    public function editSearch(Request $request, SearchServiceInterface $searchService, Search $search): JsonResponse
    {
        $errorMessage = $searchService->saveEditedSearch(
            $this->getUser(),
            $search,
            $request->query->get('search')
        );

        return new JsonResponse([
            'success' => ($errorMessage === true),
            'error' => $errorMessage !== true ? $errorMessage : null,
        ]);
    }

    /**
     * @Route("/ajax/search/get/{id}", requirements={"id": "\d+"}, name="ajax_search_get", condition="request.isXmlHttpRequest()")
     * @Security("user == search.getUser()")
     */
    public function getSearch(Search $search): JsonResponse
    {
        // Initialize serializer
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        
        // Serialize search object
        $jsonContent = $serializer->serialize(
            $search,
            'json',
            ['groups' => ['search_edit']]
        );

        // Send response
        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * @Route("/ajax/search/remove/{id}", requirements={"id": "\d+"}, name="ajax_search_remove", condition="request.isXmlHttpRequest()")
     * @Security("user == search.getUser()")
     */
    public function removeSearch(Search $search): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($search);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }
}
