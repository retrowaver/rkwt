<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use App\Entity\User;

/**
 * Quick hack making user with username "demo" unable to perform any permanent actions
 */
class DemoUserHackSubscriber implements EventSubscriberInterface
{
    const ALLOWED_ROUTES = [
        'homepage',
        'item_list',
        'search_new',
        'search_edit',
        'search_list',
        'login',
        'register',
        'ajax_allegro_filters',
        'ajax_allegro_user_id',
        'ajax_allegro_username',
        'ajax_category_get',
        'ajax_search_get'
    ];

    private $tokenStorage;

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest']
        ];
    }

    public function __construct(TokenStorageInterface $t)
    {
        $this->tokenStorage = $t;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->isUserDemo() || !$event->isMasterRequest()) {
            return true;
        }

        $currentRoute = $event->getRequest()->attributes->get('_route');

        if (in_array($currentRoute, self::ALLOWED_ROUTES)) {
            return true;
        }

        if ($currentRoute === 'user_settings') {
            //
            $event->setResponse(
                new Response('Not available in demo.')
            );
        } else {
            // All other cases are AJAX-related, so return success and do nothing
            $event->setResponse(
                new JsonResponse(['success' => true])
            );
        }
    }

    private function isUserDemo()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return false;
        }

        $user = $token->getUser();
        return ($user instanceof User && $user->getUsername() === 'demo');
    }
}
