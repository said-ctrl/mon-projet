<?php
// src/Command/GenerateArticleSlugsCommand.php

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateArticleSlugsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    protected function configure()
    {
        $this->setName('generate:article-slugs')
             ->setDescription('Génère des slugs pour tous les articles existants qui n\'ont pas de slug.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Récupérer tous les articles
        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        foreach ($articles as $article) {
            // Si l'article n'a pas encore de slug, on le génère
            if (!$article->getSlug()) {
                $slug = $this->slugger->slug($article->getTitle())->lower();
                $article->setSlug($slug);
                $this->entityManager->persist($article);
            }
        }

        // Sauvegarder les changements dans la base de données
        $this->entityManager->flush();

        $output->writeln('Slugs générés pour tous les articles manquants.');

        return Command::SUCCESS;
    }
}


