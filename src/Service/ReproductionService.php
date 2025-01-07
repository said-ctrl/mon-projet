<?php
namespace App\Service;

use App\Entity\Reproduction;
use Doctrine\ORM\EntityManagerInterface;

class ReproductionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Reproduction $reproduction)
    {
        $this->entityManager->persist($reproduction);
        $this->entityManager->flush();
    }
}
