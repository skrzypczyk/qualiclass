<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SubscriptionRedirectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RouterInterface $router
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();         // Exemple : /backoffice/dashboard
        $route = $request->attributes->get('_route'); // Exemple : app_backoffice_dashboard

        // Récupérer l'utilisateur via le token
        $token = $this->tokenStorage->getToken();
        $user = $token && $token->getUser() instanceof User ? $token->getUser() : null;
        $school = $user ? $user->getSchool() : null;

        // Autorisé si aucun utilisateur ou SUPER_ADMIN
        if (!$user || in_array("ROLE_SUPER_ADMIN", $user->getRoles(), true)) {
            return;
        }

        if($school->isFreeAccount()) {
            return;
        }

        if($route === 'app_subscription_success' ) {
            // Autorisé pour la création de souscription
            return;
        }

        $now = (new \DateTimeImmutable())->setTime(0, 0);
        $lastInvoice = $school->getLastInvoiceValid();


        if(in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            //il s'agit d'un utilisateur admin
            if (str_starts_with($path, '/backoffice') && !$lastInvoice) {
                $event->setController(function () {
                    return new RedirectResponse($this->router->generate('app_new_subscription'));
                });
            }else if (str_starts_with($path, '/subscription/show') && !$lastInvoice) {
                $event->setController(function () {
                    return new RedirectResponse($this->router->generate('app_new_subscription'));
                });
            }else if (str_starts_with($path, '/subscription/new') && $lastInvoice) {
                $event->setController(function () {
                    return new RedirectResponse($this->router->generate('app_dashboard'));
                });
            }
        }


    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [['onKernelController', 10]],
        ];
    }
}
