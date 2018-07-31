<?php

namespace App\Controller;

use App\Form\UserLoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route({"pl": "/logowanie"}, name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator)
    {
	   	 if ($authenticationUtils->getLastAuthenticationError()) {
	   	 	$this->addFlash(
	            'error',
	            $translator->trans('message.wrong-credentials')
	   	 	);
	   	 }

    	$form = $this->createForm(UserLoginType::class);

		return $this->render('security/login.html.twig', [
		    'form' => $form->createView()
		]);
    }
}
