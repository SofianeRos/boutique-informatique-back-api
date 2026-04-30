<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/api/create-checkout-session', name: 'api_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Assurez-vous d'avoir défini STRIPE_SECRET_KEY dans votre fichier .env
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_votre_cle_secrete';
        Stripe::setApiKey($stripeSecretKey);

        $data = json_decode($request->getContent(), true);
        $cart = $data['cart'] ?? []; // Supposons que le frontend envoie {"cart": [...]}

        $lineItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $itemPrice = $item['price'] ?? 0;
            $itemQuantity = $item['quantity'] ?? 1;

            $total += $itemPrice * $itemQuantity;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'] ?? 'Produit',
                    ],
                    'unit_amount' => (int) ($itemPrice * 100), // En centimes !
                ],
                'quantity' => $itemQuantity,
            ];
        }

        if (empty($lineItems)) {
            return new JsonResponse(['error' => 'Le panier est vide'], 400);
        }

        // --- 1. Création de la commande en BDD ---
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], 401);
        }

        $order = new Order();
        $order->setReference(uniqid('CMD_'));
        $order->setStatut('Attente de paiement');
        $order->setDateCommande(new \DateTime());
        $order->setTotal((string) $total);
        $order->setUser($user);

        $entityManager->persist($order);

        // --- 2. Création des détails de la commande ---
        foreach ($cart as $item) {
            // On s'assure d'avoir bien l'ID du produit dans le panier React (`item.id` par ex)
            if (!isset($item['id'])) {
                continue; // Ou renvoyer une erreur
            }

            // On récupère le vrai produit depuis la BDD
            $product = $entityManager->getRepository(Product::class)->find($item['id']);

            if ($product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setCommande($order);
                $orderDetail->setProduct($product);
                $orderDetail->setQuantite($item['quantity'] ?? 1);
                $orderDetail->setPrixUnitaire((string) ($item['price'] ?? 0));

                $entityManager->persist($orderDetail);
            }
        }

        $entityManager->flush();
        // ----------------------------------------

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
