<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;









use App\Service\Tools\NotificationsService;
use App\Service\Tools\NotificationsServiceInterface;





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

    /**
     * @Route("/test", name="testestestsetest")
     */
    public function test(NotificationsServiceInterface $n)
    {
        $n->sendNotifications();
        return $this->render('index/index.html.twig');
    }
}
