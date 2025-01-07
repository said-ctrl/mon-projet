<?php
namespace App\Entity;

use App\Repository\ReproductionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReproductionRepository::class)]
class Reproduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;  // Ajout de la propriété 'nom'

    #[ORM\Column(type: 'string', length: 255)]
    private $email; // Ajout de la propriété 'email'

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    

    // Correction ici : Relation ManyToOne vers Product (un produit peut avoir plusieurs reproductions)
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "reproductions")]
    #[ORM\JoinColumn(nullable: false)] // Un produit est nécessaire
    private $product;

    #[ORM\Column(type: 'integer')]
    private $quantite; // Ajout de la propriété 'quantite'
     // Relation OneToMany avec Avis
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

   

    // Getter et setter pour la relation ManyToOne avec Product
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }
}
