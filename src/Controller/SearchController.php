<?php

namespace App\Controller;

use App\Entity\Search;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SearchController extends AbstractController
{
    /**
     * @Route({"pl": "/wyszukiwania/dodaj"}, name="search_new")
     */
    public function newSearch(AllegroServiceInterface $allegro)
    {
        return $this->render('search/search_new.html.twig');
    }

    /**
     * @Route({"pl": "/wyszukiwania/edytuj/{id}"}, requirements={"id": "\d+"}, name="search_edit")
     * @Security("user == search.getUser()")
     */
    public function editSearch(Search $search)
    {
        return $this->render('search/search_edit.html.twig', [
            'searchId' => $search->getId(), // Data will be retrieved later using ajax
            'searchName' => $search->getName()
        ]);
    }

    /**
     * @Route({"pl": "/wyszukiwania/lista"}, name="search_list")
     */
    public function searchList()
    {
        return $this->render('search/search_list.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
