<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugService
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    // Fonction pour générer un slug unique
    public function generateSlug(string $text): string
    {
        return $this->slugger->slug($text)->lower();
    }
}
