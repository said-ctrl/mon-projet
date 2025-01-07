<?php

namespace App\Controller;

use App\Entity\Identifiant;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injection de l'Entity Manager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Rendre la vue avec les informations de l'utilisateur connecté
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_edit_profile')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager):Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user instanceof Identifiant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Créer un formulaire de modification de profil
        $form = $this->createForm(ProfileType::class, $user);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si un mot de passe est fourni, on le hache avant de l'enregistrer
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                // Hachage du mot de passe avec le service UserPasswordHasherInterface
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword); // Définir le mot de passe haché
            }else {
                // Si aucun mot de passe n'est fourni, ne pas changer le mot de passe
                // Si tu veux, tu peux aussi ajouter un message ou un comportement spécifique ici
            }

            // Sauvegarder les données de l'utilisateur dans la base de données
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            // Rediriger vers la page du profil
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

   

