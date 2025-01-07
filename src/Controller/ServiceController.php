<?php

namespace App\Controller;

use App\Entity\DemandeConseil;
use App\Entity\DemandeCreation;
use App\Entity\Reproduction;
use App\Form\DemandeConseilType;
use App\Form\DemandeCreationType;
use App\Form\ReproductionType;
use App\Service\MailerService;
use App\Service\DemandeConseilService;
use App\Service\DemandeCreationService;
use App\Service\ReproductionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    private $mailerService;
    private $demandeConseilService;
    private $demandeCreationService;
    private $reproductionService;

    // Injection des services
    public function __construct(
        MailerService $mailerService,
        DemandeConseilService $demandeConseilService,
        DemandeCreationService $demandeCreationService,
        ReproductionService $reproductionService
    ) {
        $this->mailerService = $mailerService;
        $this->demandeConseilService = $demandeConseilService;
        $this->demandeCreationService = $demandeCreationService;
        $this->reproductionService = $reproductionService;
    }

    // Route pour la page d'accueil du service
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig');
    }

    // Route pour la demande de conseil en marketing
    #[Route('/service/demande-conseil', name: 'demande_conseil')]
    public function createDemandeConseil(Request $request): Response
    {
        $demandeConseil = new DemandeConseil();
        $form = $this->createForm(DemandeConseilType::class, $demandeConseil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du service pour persister l'entité
            $this->demandeConseilService->save($demandeConseil);

            // Envoi d'un e-mail après l'ajout de la donnée
            $this->mailerService->sendEmailAlert(
                'Nouvelle Demande de Conseil',
                'fifinabs@gmail.com',
                'Une nouvelle demande de conseil a été soumise.'
            );

            // Redirection après la soumission réussie
            return $this->redirectToRoute('app_service');
        }

        return $this->render('service/demande_conseil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour la demande de création d'œuvre
    #[Route('/service/demande-creation', name: 'demande_creation')]
    public function createDemandeCreation(Request $request): Response
    {
        $demandeCreation = new DemandeCreation();
        $form = $this->createForm(DemandeCreationType::class, $demandeCreation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du service pour persister l'entité
            $this->demandeCreationService->save($demandeCreation);

            // Envoi d'un e-mail après l'ajout de la donnée
            $this->mailerService->sendEmailAlert(
                'Nouvelle Demande de Création',
                'fifinabs@gmail.com',
                'Une nouvelle demande de création a été soumise.'
            );

            $this->addFlash('success', 'Demande de création soumise avec succès!');

            // Redirection après la soumission réussie
            return $this->redirectToRoute('app_service');
        }

        return $this->render('service/demande_creation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour la commande de reproduction
    #[Route('/service/reproduction', name: 'reproduction')]
    public function createReproduction(Request $request): Response
    {
        $reproduction = new Reproduction();
        $form = $this->createForm(ReproductionType::class, $reproduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du service pour persister l'entité
            $this->reproductionService->save($reproduction);

            // Envoi d'un e-mail après l'ajout de la donnée
            $this->mailerService->sendEmailAlert(
                'Nouvelle Commande de Reproduction',
                'fifinabs@gmail.com',
                'Une nouvelle commande de reproduction a été soumise.'
            );

            $this->addFlash('success', 'La reproduction a été ajoutée avec succès.');

            // Redirection après la soumission réussie
            return $this->redirectToRoute('product_list');
        }

        return $this->render('service/reproduction.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   
}

