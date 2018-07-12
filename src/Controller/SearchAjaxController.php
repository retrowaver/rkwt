<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use App\Service\SearchServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;




use App\Entity\Search;
use App\Entity\Item;



use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
// For annotations
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
     * @Route("/ajax/search/filters", name="ajax_get_available_filters")
     */
    public function getAvailableFilters(Request $request, AllegroServiceInterface $allegro): JsonResponse
    {
        $currentFilters = $request->query->get('currentFilters');

        return new JsonResponse($allegro->getFiltersInfo($currentFilters));
    }

    /**
     * @Route("/ajax/search/save", name="ajax_search_save")
     */
    public function saveSearch(Request $request, SearchServiceInterface $searchService): JsonResponse
    {
        $search = $searchService->denormalizeSearch($request->query->get('search'));

        $errorMessage = $searchService->validateSearch($search);
        if (!is_string($errorMessage)) {
            // Persist

            $search->setUser($this->getUser());
            $search->setStatus(666); //

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($search);
            $entityManager->flush();
        }

        return new JsonResponse([
            'success' => ($errorMessage === true),
            'error' => $errorMessage !== true ? $errorMessage : null,
        ]);

    }

    /**
     * @Route("/ajax/search/edit/{id}", requirements={"id": "\d+"}, name="ajax_search_edit")
     * @Security("user == search.getUser()")
     */
    public function editSearch(Request $request, SearchServiceInterface $searchService, Search $search): JsonResponse
    {
        $editedSearch = $searchService->denormalizeSearch($request->query->get('search'));

        $search->setName($editedSearch->getName());
        $search->setFilters($editedSearch->getFilters());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route("/ajax/search/get/{id}", requirements={"id": "\d+"}, name="ajax_search_get")
     */
    public function getSearch(Search $search): JsonResponse
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];


        //$normalizers = [new ObjectNormalizer($classMetadataFactory)];
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $normalizers = [$normalizer];


        $serializer = new Serializer($normalizers, $encoders);

        /*$normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });*/

        $jsonContent = $serializer->serialize(
            $search,
            'json',
            ['groups' => ['search_edit']]
        );

        //echo $jsonContent;
        //exit;
        return new JsonResponse($jsonContent, 200, [], true);


        /*$callback = function ($filters) {
            return 'filters placeholder';
        };*/
        //$normalizer->setCallbacks(['filters' => $callback]);

        //$normalizer->setCircularReferenceLimit(2);
        // Add Circular reference handler


        //$normalizer->setIgnoredAttributes(array('user', 'items'));


        //$normalizers = array($normalizer);
        //$serializer = new Serializer($normalizers, $encoders);


        //$search->setUser(null);



        //$dupa = new Search();
        //$dupa->setName('chuj ci na imie');

        //dump($dupa);

        /*$jsonContent = $serializer->serialize(
            $dupa,
            'json',
            ['groups' => ['dupa']]
        );*/

        //$jsonContent = $serializer->serialize($dupa, 'json', ['groups' => ['test']]);
        //$jsonContent = $serializer->serialize($dupa, 'json');

        //dump($search);
        //exit;
        //echo $jsonContent;
        //dump($search);
        //exit;

        /*return new JsonResponse([
            'success' => true
        ]);*/
    }

    /**
     * @Route("/ajax/search/remove/{id}", requirements={"id": "\d+"}, name="ajax_search_remove")
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
