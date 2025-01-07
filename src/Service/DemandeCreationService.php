<?php
namespace App\Service;

use App\Entity\DemandeCreation;
use Doctrine\ORM\EntityManagerInterface;

class DemandeCreationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(DemandeCreation $demandeCreation)
    {
        $this->entityManager->persist($demandeCreation);
        $this->entityManager->flush();
    }
}
