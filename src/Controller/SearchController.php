<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Allegro\AllegroServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SearchController extends AbstractController
{
    /**
     * @Route("/search/new", name="search_new")
     */
    public function newSearch(AllegroServiceInterface $allegro)
    {
        return $this->render('search/search_new.html.twig');
    }

    /**
     * @Route("/search/edit/{id}", requirements={"id": "\d+"}, name="search_edit")
     * @Security("user == search.getUser()")
     */
    public function editSearch(Search $search)
    {
        return $this->render('search/search_edit.html.twig', [
            'searchId' => $search->getId(), // data will be retrieved later using ajax
            'searchName' => $search->getName()
        ]);
    }

    /**
     * @Route("/search/list", name="search_list")
     */
    public function searchList()
    {
        return $this->render('search/search_list.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
