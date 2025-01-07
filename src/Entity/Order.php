<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?identifiant $identifiant = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'enregistrer';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cartorder')]
    private Collection $cartItems;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shippingAdress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentMethod = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'panier')]
    private Collection $cartOrder;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->cartOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?identifiant
    {
        return $this->identifiant;
    }

    public function setIdentifiant(?identifiant $identifiant): static
    {
        $this->identifiant = $identifiant;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
            $cartItem->setCartorder($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getCartorder() === $this) {
                $cartItem->setCartorder(null);
            }
        }

        return $this;
    }

    public function getShippingAdress(): ?string
    {
        return $this->shippingAdress;
    }

    public function setShippingAdress(string $shippingAdress): static
    {
        $this->shippingAdress = $shippingAdress;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartOrder(): Collection
    {
        return $this->cartOrder;
    }

    public function addCartOrder(CartItem $cartOrder): static
    {
        if (!$this->cartOrder->contains($cartOrder)) {
            $this->cartOrder->add($cartOrder);
        }

        return $this;
    }

    public function removeCartOrder(CartItem $cartOrder): static
    {
        if ($this->cartOrder->removeElement($cartOrder)) {
           
        }

        return $this;
    }
}
