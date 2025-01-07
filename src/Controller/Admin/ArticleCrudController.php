<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            // Configure les champs de l'entité Article à afficher dans EasyAdmin
            TextField::new('title')->setLabel('Titre'),
            TextEditorField::new('content')->setLabel('Contenu'),
            DateTimeField::new('createdAt')->setLabel('Date de création'),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des Articles')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un Article')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un Article')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails de l\'Article');
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
