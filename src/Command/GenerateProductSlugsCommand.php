<?php

namespace App\Command;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateProductSlugsCommand extends Command
{
    private $entityManager;
    private $productRepository;
    private $slugger;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository, SluggerInterface $slugger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->slugger = $slugger;
    }

    protected function configure()
    {
        $this->setName('app:generate-product-slugs')
            ->setDescription('Génère les slugs pour tous les produits qui n\'en ont pas.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Mise à jour des slugs des produits en cours...');

        // Récupérer tous les produits
        $products = $this->productRepository->findAll();

        $updatedCount = 0;

        foreach ($products as $product) {
            // Vérifiez si le produit n'a pas déjà de slug
            if (!$product->getSlug()) {
                $slug = $this->slugger->slug($product->getTitle())->lower();
                
                // Si le slug existe déjà, on ajoute un suffixe pour le rendre unique
                $existingProduct = $this->productRepository->findOneBy(['slug' => $slug]);
                if ($existingProduct) {
                    $slug .= '-' . uniqid();
                }

                // Affecter le slug au produit
                $product->setSlug($slug);

                // Sauvegarder le produit mis à jour
                $this->entityManager->persist($product);
                $updatedCount++;
            }
        }

        // Enregistrer les changements
        $this->entityManager->flush();

        $output->writeln("$updatedCount slugs ont été mis à jour avec succès.");
        return Command::SUCCESS;
    }
}
