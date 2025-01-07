<?php

namespace App\Controller\Admin;

use App\Entity\Identifiant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IdentifiantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Identifiant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des Identifiants')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un Identifiant')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails de l\'Identifiant');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            TextField::new('nom')->setLabel('Nom d\'utilisateur'),
            TextEditorField::new('prenom')->setLabel('Prenom d\'utilisateur'),

            // Utilisation de ChoiceField pour les rôles
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Artiste' => 'ROLE_ARTIST',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->setLabel('Rôle')
                ->allowMultipleChoices(true) // Permet de sélectionner plusieurs rôles si nécessaire
                ->setFormTypeOption('expanded', true) // Affiche les choix sous forme de cases à cocher
                ->setFormTypeOption('multiple', true), // Permet plusieurs choix de rôle
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
         // Suppression de l'ajout manuel de l'action "edit" sur la page d'index
         $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            // Vous pouvez personnaliser l'action "edit" ici si nécessaire
            return $action->setLabel('Modifier');
        });
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->setLabel('Supprimer');
        });
        // Personnalisation des actions (si nécessaire)
        return $actions;
    }
}
