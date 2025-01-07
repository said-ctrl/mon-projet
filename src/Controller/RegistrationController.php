<?php

namespace App\Controller;

use App\Entity\Identifiant;
use App\Form\RegistrationFormType;
use App\Repository\IdentifiantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;  // Ajoute l'import pour l'EntityManagerInterface

class RegistrationController extends AbstractController
{
    private $entityManager;
    private $identifiantRepository;

    public function __construct(EntityManagerInterface $entityManager, IdentifiantRepository $identifiantRepository)
    {
        $this->entityManager = $entityManager;
        $this->identifiantRepository = $identifiantRepository;
    }
    #[Route('/registration', name: 'app_registration')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Créer une instance de l'entité Identifiant
        $user = new Identifiant();

        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Gérer la requête (saisie du formulaire)
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $this->identifiantRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                // Si l'email existe déjà, afficher un message d'erreur
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_registration');
            }
            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword); // Enregistrer le mot de passe haché
            // Récupérer le rôle sélectionné
            $roles = $form->get('roles')->getData();
            if (is_array($roles)) {
                $user->setRoles($roles);  // Assigner directement les rôles
            } else {
                // Gérer le cas où ce n'est pas un tableau, au cas où l'utilisateur n'en a pas sélectionné
                $user->setRoles(['ROLE_USER']); // Définir un rôle par défaut

            }
            // Enregistrer l'utilisateur dans la base de données
            $this->entityManager->persist($user); // Persiste l'entité
            $this->entityManager->flush(); // Sauvegarde l'entité dans la base de données

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre inscription a été effectuée avec succès.');

            // Rediriger l'utilisateur vers la page de connexion après inscription
            return $this->redirectToRoute('app_login');
        }

        // Rendre la vue avec le formulaire d'inscription
        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
