<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController

{
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }
    #[Route('/home', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les produits
        $allProducts = $entityManager->getRepository(Product::class)->findAll();
        // Vérifier si des produits existent
        if (count($allProducts) > 0) {
            // Mélanger les produits de manière aléatoire
            shuffle($allProducts);

            // Limiter à 5 produits
            $randomProducts = array_slice($allProducts, 0, 5);
        } else {
            // Si aucun produit n'est trouvé, on peut initialiser une liste vide ou gérer autrement
            $randomProducts = [];
        }
        // Récupérer les avis clients
        // $avisRepository = $entityManager->getRepository(Avis::class);
        $avisClients = $this->reviewRepository->findBy(
            [],
            ['datetime' => 'DESC'],
            3
        );
        $isLoggedIn = $this->isGranted('IS_AUTHENTICATED_FULLY');

        // Passer les produits aléatoires à la vue
        return $this->render('home/index.html.twig', [
            'random_products' => $randomProducts,
            'avis_clients' => $avisClients,
            'is_logged_in' => $isLoggedIn,

        ]);
    }



    #[Route('/home/cvart', name: 'cvart')]
    public function cvart(): Response
    {
        return $this->render('home/cvart.html.twig');
    }

    #[Route('/home/cvmarket', name: 'cvmarket')]
    public function cvmarket(): Response
    {
        return $this->render('home/cvmarket.html.twig');
    }



    #[Route('/submit-avis', name: 'submit_avis', methods: ['POST'])]
    public function submitAvis(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $message = $request->request->get('message');
        $type = $request->request->get('type');

        if (!$nom || !$email || !$message || !$type) {
            $this->addFlash('error', 'Tous les champs doivent être remplis.');
            return $this->redirectToRoute('home');
        }

        $review = new Review();
        $review->setNom($nom);
        $review->setEmail($email);
        $review->setMessage($message);
        $review->setType($type);
        $review->setDatetime(new \DateTime());

        try {
            $entityManager->persist($review);
            $entityManager->flush();
            $this->addFlash('success', 'Votre avis a bien été envoyé.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }
}
