<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Form\UserLoginType;

class SecurityController extends AbstractController
{
    /**
     * @Route({"pl": "/logowanie"}, name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
	   	 if ($authenticationUtils->getLastAuthenticationError()) {
	   	 	$this->addFlash(
	            'error',
	            'Nieprawidłowe dane.'
	   	 	);
	   	 }

    	$form = $this->createForm(UserLoginType::class);

		return $this->render('security/login.html.twig', [
		    'form' => $form->createView()
		]);
    }
}
