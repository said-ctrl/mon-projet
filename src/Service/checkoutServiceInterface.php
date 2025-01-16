<?php

namespace App\Service;

use App\Entity\Identifiant;

interface checkoutServiceInterface {
    public function createCheckout($cart, Identifiant $user);
    public function webhookSuccess(): mixed;
}