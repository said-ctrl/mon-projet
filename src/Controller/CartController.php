<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CheckoutType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    private CartService $cartService;
    private EntityManagerInterface $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $identifiant = $this->getUser();
        if (!$identifiant) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $this->cartService->getUserCart($identifiant);

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }
    #[Route('/cart/add/{productId}', name: 'add_to_cart')]
    public function addToCart(int $productId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return $this->redirectToRoute('app_cart');
        }

        $cartItem = $this->entityManager->getRepository(CartItem::class)->findOneBy([
            'identifiant' => $user,
            'product' => $product,
        ]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setIdentifiant($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $cartItem->setStatus('enregistré');
        }

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove/{cartItemId}', name: 'remove_from_cart')]
    public function removeFromCart(int $cartItemId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItem = $this->entityManager->getRepository(CartItem::class)->find($cartItemId);

        if ($cartItem && $cartItem->getIdentifiant() === $user) {
            $this->entityManager->remove($cartItem);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'checkout_cart')]
    public function checkout(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $this->cartService->getUserCart($user);

        if (!$cartItems) {
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setIdentifiant($user);
        $order->setStatus('enregistrée');
        $order->setCreatedAt(new \DateTimeImmutable());


        $form = $this->createForm(CheckoutType::class, $order, [
            'user' => $user,  // Passer l'utilisateur dans les options
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide
            $this->entityManager->persist($order);

            foreach ($cartItems as $item) {
                $item->setCartorder($order);
                $item->setStatus('enregistrée');
                $this->entityManager->persist($item);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('order_confirmation');
        }
        return $this->render('cart/checkout.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }
    #[Route('/order', name: 'order_index')]
    public function orders(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $orders = $this->entityManager->getRepository(Order::class)->findBy(['identifiant' => $user]);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/order/confirmation', name: 'order_confirmation')]
    public function orderConfirmation(): Response
    {
        return $this->render('order/confirmation.html.twig');
    }
}
