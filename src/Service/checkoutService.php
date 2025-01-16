<?php

namespace App\Service;

use App\Entity\Identifiant;
use App\Entity\Order;
use App\Repository\IdentifiantRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class checkoutService implements checkoutServiceInterface
{


    private $stripe;

    public function __construct(
        readonly private string $stripe_secret_key,
        readonly private string $stripe_webhook,
        readonly private OrderRepository $orderRepository,
        readonly private UrlGeneratorInterface $urlGeneratorInterface,
        readonly private RequestStack $requestStack,
        readonly private EntityManagerInterface $entityManagerInterface,
        readonly private Security $security,
        readonly private Filesystem $filesystem,
        readonly private IdentifiantRepository $identifiantRepository,
        readonly private CartService $cartService
    ) {
        $this->stripe = new StripeClient($stripe_secret_key);
    }


    public function createCheckout($cart, Identifiant $user)
    {

        $stripe = $this->stripe->checkout->sessions->create([
            'success_url' => $this->urlGeneratorInterface->generate("order_confirmation", [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGeneratorInterface->generate("order_cancel", [], UrlGeneratorInterface::ABSOLUTE_URL),
            'line_items' => [
                $this->lineItems($cart),
            ],
            'mode' => 'payment',
            'metadata' => ['user_id' => (string) $user->getId()],
            "customer_email"=>$user->getEmail(),

        ]);

        return $stripe->url;
    }

    private function lineItems($cart)
    {

        foreach ($cart as $item) {
            $product["price_data"]["currency"] = "eur";
            $product["price_data"]["product_data"]["name"] = $item->getProduct()->getTitle();
            $product["price_data"]["unit_amount"] = $item->getProduct()->getPrice() * 100;
            $product["quantity"] = $item->getQuantity();

            $items[] = $product;
        }

        return $items;
    }

    public function webhookSuccess(): mixed
    {

        $sig_header = $this->requestStack->getMainRequest()->headers->get("Stripe-Signature");
        $payload = @file_get_contents('php://input');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $this->stripe_webhook
            );

            $userId = $event->data->object->metadata->user_id;  // Assurez-vous que cet ID est bien présent dans les métadonnées

            if ($event->data->object->status === "succeeded" && $event->type === "charge.updated") {
                $this->filesystem->appendToFile("data.txt", $event);
                $order = new Order();
                $order->setStatus($event->data->object->status);
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setTotalAmount($event->data->object->amount / 100);
                $order->setShippingAdress('Adresse de livraison ici');
                $order->setPaymentMethod($event->data->object->id);
                $order->setIdentifiant($userId);

                $this->entityManagerInterface->persist($order);
                $this->entityManagerInterface->flush();

                return $event->data->object;
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            echo $e->getMessage();
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            echo $e->getMessage();
            http_response_code(400);
            exit();
        }
    }
}
