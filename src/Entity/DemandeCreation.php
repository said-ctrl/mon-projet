<?php

namespace App\Entity;

use App\Repository\DemandeCreationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeCreationRepository::class)]
class DemandeCreation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $preferences = null;

     #[ORM\Column(length: 255, nullable: true)]
     private ?string $type_oeuvre = null;
      // Relation OneToMany avec Avis
  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPreferences(): ?string
    {
        return $this->preferences;
    }

    public function setPreferences(string $preferences): static
    {
        $this->preferences = $preferences;

        return $this;
    }
    public function getTypeOeuvre(): ?string
    {
        return $this->type_oeuvre;
    }

    public function setTypeOeuvre(?string $type_oeuvre): static
    {
        $this->type_oeuvre = $type_oeuvre;

        return $this;
    }
}
