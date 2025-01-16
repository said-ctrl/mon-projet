<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Form\CheckoutType;
use App\Service\CartService;
use App\Service\checkoutServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    private CartService $cartService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CartService $cartService,
        EntityManagerInterface $entityManager,
        readonly private checkoutServiceInterface $checkoutServiceInterface,
    ) {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $identifiant = $this->getUser();
        if (!$identifiant) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $this->cartService->getUserCart($identifiant);

        $total = 0;
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            $total += $product->getPrice() * $cartItem->getQuantity();
        }

        $form = $this->createForm(CheckoutType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('checkout_cart');
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total, // Passer le total à la vue
            "form" => $form->createView(),
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
    public function checkout(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $this->cartService->getUserCart($user);

        if (!$cartItems) {
            return $this->redirectToRoute('app_cart');
        }

        $url = $this->checkoutServiceInterface->createCheckout($cartItems, $this->getUser());

        return $this->redirect($url);
    }


    #[Route('/order/confirmation', name: 'order_confirmation')]
    public function orderConfirmation(): Response
    {
        return $this->render('order/confirmation.html.twig');
    }

    #[Route('/order/cancel', name: 'order_cancel')]
    public function orderCancel(): Response
    {


        return $this->render('order/cancel.html.twig');
    }
}
