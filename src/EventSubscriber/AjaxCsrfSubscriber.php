<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AjaxCsrfSubscriber implements EventSubscriberInterface
{
    private $tokenManager;

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 1000]
        ];
    }


    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function onKernelRequest(GetResponseEvent $e)
    {
        if (
            substr($e->getRequest()->getPathInfo(), 0, 6) === '/ajax/'
        ) {
            $token = new CsrfToken('ajax', $e->getRequest()->query->get('csrfToken'));

            if (!$this->tokenManager->isTokenValid($token)) {
                $e->setResponse(new Response('The CSRF token is invalid', 400));
            }
        }
    }
}
