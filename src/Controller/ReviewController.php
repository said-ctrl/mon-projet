<?php

namespace App\Controller;

use App\Entity\Identifiant;
use App\Entity\Review;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/review/new', name: 'new_review')]
    public function new(Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(AvisType::class, $review,  [
            'is_logged_in' => $this->isGranted('IS_AUTHENTICATED_FULLY'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de la date automatique
            $review->setDatetime(new \DateTime());
            if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->getUser();
                if ($user instanceof Identifiant) {
                    $review->setEmail($user->getEmail());
                } else {
                    throw new \Exception('L\'utilisateur n\'est pas de type Identifiant');
                }
            }

            // Sauvegarder l'avis en base
            $this->entityManager->persist($review);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre avis a bien été enregistré.');

            return $this->redirectToRoute('new_review');
        }

        return $this->render('review/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
