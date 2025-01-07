<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean')]
    private bool $reserved = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $reservationDate = null;

    // Relation OneToMany : un produit peut avoir plusieurs reproductions
    #[ORM\OneToMany(targetEntity: Reproduction::class, mappedBy: "product")]
    private Collection $reproductions;

   

    #[ORM\ManyToOne(targetEntity: Identifiant::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Identifiant $reservedBy = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'product')]
    private Collection $reviews;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product')]
    private Collection $cartItems;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'product')]
    private Collection $articles;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Identifiant $user = null;

   
    public function __construct()
    {
        // Initialisation des collections vides
        $this->reproductions = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    // Getter pour reproductions
    public function getReproductions(): Collection
    {
        return $this->reproductions;
    }

    // Ajout d'une reproduction
    public function addReproduction(Reproduction $reproduction): static
    {
        if (!$this->reproductions->contains($reproduction)) {
            $this->reproductions[] = $reproduction;
            $reproduction->setProduct($this);  // Lier la reproduction au produit
        }

        return $this;
    }

    // Suppression d'une reproduction
    public function removeReproduction(Reproduction $reproduction): static
    {
        if ($this->reproductions->removeElement($reproduction)) {
            // Dissocier la reproduction du produit si nécessaire
            if ($reproduction->getProduct() === $this) {
                $reproduction->setProduct(null);
            }
        }

        return $this;
    }

    // Getter et setter pour les autres propriétés

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
    public function getReserved(): bool
    {
        return $this->reserved;
    }

    public function setReserved(bool $reserved): self
    {
        $this->reserved = $reserved;
        return $this;
    }

    public function getReservationDate(): ?\DateTimeInterface
    {
        return $this->reservationDate;
    }

    public function setReservationDate(?\DateTimeInterface $reservationDate): self
    {
        $this->reservationDate = $reservationDate;
        return $this;
    }
    public function getReservedBy(): ?Identifiant
    {
        return $this->reservedBy;
    }

    public function setReservedBy(?Identifiant $user): self
    {
        $this->reservedBy = $user;
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setProduct($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getProduct() === $this) {
                $cartItem->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setProduct($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getProduct() === $this) {
                $article->setProduct(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getUser(): ?Identifiant
    {
        return $this->user;
    }

    public function setUser(?Identifiant $user): static
    {
        $this->user = $user;

        return $this;
    }



   
  
}
