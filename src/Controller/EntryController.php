<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EntryController extends AbstractController
{
    #[Route('/', name: 'entry')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $allProducts = $entityManager->getRepository(Product::class)->findAll();

        if (count($allProducts) > 0) {
            shuffle($allProducts);
            $randomProducts = array_slice($allProducts, 0, 5);
        } else {
            $randomProducts = [];
        }
        return $this->render('entry/index.html.twig', [
            'products' => $randomProducts,
            'controller_name' => 'EntryController',
        ]);
    }
}
