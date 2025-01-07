<?php

namespace App\EventListener;

use App\Service\ProductService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckReservationExpirationListener implements EventSubscriberInterface
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Vérifiez si la requête est pour la page d'accueil (ou une autre page spécifique)
        $request = $event->getRequest();
        if ($request->getPathInfo() === '/') {
            // Appel de votre fonction de vérification des expirations
            $this->productService->checkReservationExpiration();
        }
    }

    public static function getSubscribedEvents()
    {
        // Écoute de l'événement "kernel.request" à chaque requête HTTP
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
