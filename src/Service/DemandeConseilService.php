<?php
namespace App\Service;

use App\Entity\DemandeConseil;
use Doctrine\ORM\EntityManagerInterface;

class DemandeConseilService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(DemandeConseil $demandeConseil)
    {
        $this->entityManager->persist($demandeConseil);
        $this->entityManager->flush();
    }
}
