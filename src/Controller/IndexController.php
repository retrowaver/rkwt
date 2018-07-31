<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('item_list');
        }

        return $this->render('index/index.html.twig');
    }
}
