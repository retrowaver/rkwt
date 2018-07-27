<?php

namespace App\Controller\Ajax;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SearchServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Search;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;




////////////////
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class SearchAjaxController extends AbstractController
{
    /**
     * @Route("/ajax/search/save", name="ajax_search_save", condition="request.isXmlHttpRequest()")
     */
    public function saveSearch(Request $request, SearchServiceInterface $searchService): JsonResponse
    {
        $search = $searchService->denormalizeSearch($request->query->get('search'));
        $searchService->sanitizeSearch($search);

        $errorMessage = $searchService->validateSearch($search);
        if (!is_string($errorMessage)) {
            // Persist
            $search->setUser($this->getUser());
            $search->setStatus(0); //

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($search);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Dodano wyszukiwanie!'
            );
        }

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
        $editedSearch = $searchService->denormalizeSearch($request->query->get('search'));
        $searchService->sanitizeSearch($editedSearch);

        $search->setName($editedSearch->getName());
        $search->setFilters($editedSearch->getFilters());

        $search->setStatus(0);
        $search->setTimeLastSearched(null);
        $search->setTimeLastFullySearched(null);

        $errorMessage = $searchService->validateSearch($search);
        if (!is_string($errorMessage)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Zapisano zmiany!'
            );
        }

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
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];

        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $normalizers = [$normalizer];


        $serializer = new Serializer($normalizers, $encoders);
        
        $jsonContent = $serializer->serialize(
            $search,
            'json',
            ['groups' => ['search_edit']]
        );

        //echo $jsonContent;
        //exit;
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