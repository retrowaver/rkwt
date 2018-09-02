<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use App\Entity\User;

/**
 * Redirects logged in users from anonymous pages (login page, etc.)
 */
class AnonymousRedirectSubscriber implements EventSubscriberInterface
{
    const ANONYMOUS_ONLY_ROUTES = [
        'register',
        'login'
    ];

    private $tokenStorage;
    private $router;

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest']
        ];
    }

    public function __construct(TokenStorageInterface $t, RouterInterface $r)
    {
        $this->tokenStorage = $t;
        $this->router = $r;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->isUserLogged() && $event->isMasterRequest()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
                $response = new RedirectResponse($this->router->generate('homepage'));
                $event->setResponse($response);
            }
        }
    }

    private function isUserLogged()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return false;
        }

        $user = $token->getUser();
        return $user instanceof User;
    }

    private function isAuthenticatedUserOnAnonymousPage($currentRoute)
    {
        return in_array($currentRoute, self::ANONYMOUS_ONLY_ROUTES);
    }
}
