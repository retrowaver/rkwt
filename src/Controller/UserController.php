<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserSettingsType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route({"pl": "/konto/ustawienia"}, name="user_settings")
     */
    public function settings(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
    	$user = $this->getUser();
    	$form = $this->createForm(UserSettingsType::class, $user);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		if ($user->getPlainPassword() !== null) {
    			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            	$user->setPassword($password);
    		}

    		$entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
	            'notice',
	            'Zapisano zmiany!'
       	 	);
        }

		return $this->render('user/settings.html.twig', [
		    'form' => $form->createView(),
		]);
    }
}
