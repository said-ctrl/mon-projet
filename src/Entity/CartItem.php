<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    private ?Identifiant $identifiant = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    private ?Order $cartorder = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?Identifiant
    {
        return $this->identifiant;
    }

    public function setIdentifiant(?Identifiant $identifiant): static
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCartorder(): ?Order
    {
        return $this->cartorder;
    }

    public function setCartorder(?Order $cartorder): static
    {
        $this->cartorder = $cartorder;

        return $this;
    }

   
}
