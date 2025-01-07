<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/a_propos', name: 'a_propos')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
      
    }
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('page/cgu.html.twig');
    }
    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        return $this->render('page/cgv.html.twig');
    }
}
