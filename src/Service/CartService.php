<?php

namespace App\Service;

use App\Entity\Identifiant;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;

class CartService
{
    private ProductRepository $productRepository;
    private CartItemRepository $cartItemRepository;

    public function __construct(ProductRepository $productRepository, CartItemRepository $cartItemRepository)
    {
        $this->productRepository = $productRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    public function addToCart(Identifiant $identifiant, int $productId, int $quantity): void
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new \Exception("Produit introuvable");
        }

        $cartItem = $this->cartItemRepository->findOneBy(['identifiant' => $identifiant, 'product' => $product]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setIdentifiant($identifiant);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
        }

        $this->cartItemRepository->save($cartItem, true);
    }

    public function getUserCart(Identifiant $identifiant): array
    {
        return $this->cartItemRepository->findBy(['identifiant' => $identifiant]);
    }
}
