<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MaillerInterController extends AbstractController
{
    #[Route('/mailler/inter', name: 'app_mailler_inter')]
    public function index(): Response
    {
        return $this->render('mailler_inter/index.html.twig', [
            'controller_name' => 'MaillerInterController',
        ]);
    }
}
