<?php

namespace App\Controller;

use App\Service\checkoutServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{
    public function __construct(
        public checkoutServiceInterface $checkoutServiceInterface,
    ) {
        $this->checkoutServiceInterface = $checkoutServiceInterface;
    }


    #[Route('/webhook', name: 'app_webhook', methods: ["POST"])]
    public function index(Filesystem $filesystem): JsonResponse
    {
        $event = $this->checkoutServiceInterface->webhookSuccess();
        
        if ($event) {
           return $this->json("sucess", 200);
        }else{
            return $this->json("Error", 500);
        }

    }
}
