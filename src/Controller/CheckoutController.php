<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/api/create-checkout-session', name: 'api_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request): JsonResponse
    {
        // Assurez-vous d'avoir défini STRIPE_SECRET_KEY dans votre fichier .env
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_votre_cle_secrete';
        Stripe::setApiKey($stripeSecretKey);

        $data = json_decode($request->getContent(), true);
        $cart = $data['cart'] ?? []; // Supposons que le frontend envoie {"cart": [...]}

        $lineItems = [];

        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'] ?? 'Produit',
                    ],
                    'unit_amount' => (int) (($item['price'] ?? 0) * 100), // En centimes !
                ],
                'quantity' => $item['quantity'] ?? 1,
            ];
        }

        if (empty($lineItems)) {
            return new JsonResponse(['error' => 'Le panier est vide'], 400);
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                // Remplacez ces URLs avec l'URL de votre application frontend (React, Vue, Angular, etc.)
                'success_url' => 'http://localhost:5173/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:5173/cart',
            ]);

            return new JsonResponse(['url' => $session->url]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
