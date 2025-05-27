<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/subscription')]
final class SubscriptionController extends AbstractController
{
    #[Route('/new', name: 'app_new_subscription')]
    public function create(): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // IDs à charger (ou tu peux charger tous les produits si tu préfères)
        $productIds = [
            'base' => $_ENV['STRIPE_PRODUCT_BASE'],
            'school' => $_ENV['STRIPE_PRODUCT_SCHOOL'],
            'user' => $_ENV['STRIPE_PRODUCT_USER'],
        ];

        $products = [];

        foreach ($productIds as $key => $id) {
            $product = Product::retrieve($id);
            $price = Price::all(['product' => $id, 'limit' => 1])->data[0] ?? null;

            $products[$key] = [
                'id' => $id,
                'name' => $product->name,
                'description' => $product->description,
                'price_id' => $price?->id,
                'price_amount' => $price ? $price->unit_amount / 100 : null,
                'currency' => $price?->currency ?? 'eur',
                'recurring' => $price?->recurring?->interval ?? null,
            ];
        }

        return $this->render('subscription/new.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/update/{id}', name: 'app_update_subscription')]
    public function update(
        Request $request,
        Subscription $subscription,
        EntityManagerInterface $em
    ): Response {
        if ($this->getUser() !== $subscription->getOwner()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet abonnement.');
        }

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $productIds = [
            'base' => $_ENV['STRIPE_PRODUCT_BASE'],
            'school' => $_ENV['STRIPE_PRODUCT_SCHOOL'],
            'user' => $_ENV['STRIPE_PRODUCT_USER'],
        ];
        $products = [];

        foreach ($productIds as $key => $id) {
            $product = \Stripe\Product::retrieve($id);
            $price = \Stripe\Price::all(['product' => $id, 'limit' => 1])->data[0] ?? null;

            $products[$key] = [
                'id' => $id,
                'name' => $product->name,
                'description' => $product->description,
                'price_id' => $price?->id,
                'price_amount' => $price ? $price->unit_amount / 100 : null,
                'currency' => $price?->currency ?? 'eur',
                'recurring' => $price?->recurring?->interval ?? null,
            ];
        }

        if ($request->isMethod('POST')) {
            $quantities = $request->request->all('quantities');
            $stripeSub = \Stripe\Subscription::retrieve($subscription->getStripeSubscriptionId());

            $itemsToUpdate = [];

            foreach (['school', 'user'] as $key) {
                $quantity = max(0, (int) ($quantities[$key] ?? 0));
                $productId = $productIds[$key];
                $priceId = $products[$key]['price_id'];

                $existingItem = null;
                foreach ($stripeSub->items->data as $subItem) {
                    if ($subItem->price->product === $productId) {
                        $existingItem = $subItem;
                        break;
                    }
                }

                if ($existingItem) {
                    $itemsToUpdate[] = [
                        'id' => $existingItem->id,
                        'quantity' => $quantity,
                    ];
                } elseif ($quantity > 0) {
                    $itemsToUpdate[] = [
                        'price' => $priceId,
                        'quantity' => $quantity,
                    ];
                }
            }

            try {
                \Stripe\Subscription::update($subscription->getStripeSubscriptionId(), [
                    'items' => $itemsToUpdate,
                    'proration_behavior' => 'always_invoice',
                ]);

                $this->addFlash('success', 'Votre abonnement a été mis à jour avec succès.');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour de l’abonnement : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_subscription');
        }

        return $this->render('subscription/update.html.twig', [
            'products' => $products,
            'subscription' => $subscription
        ]);
    }



    #[Route('/create', name: 'app_subscription_store', methods: ['POST'])]
    public function store(
        Request $request,
        Security $security
    ): Response {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $user = $security->getUser();
        $quantities = $request->request->all('quantities');

        // Mapping entre les clés HTML et les ID Stripe (depuis variables d'env)
        $productIds = [
            'base' => $_ENV['STRIPE_PRODUCT_BASE'],
            'school' => $_ENV['STRIPE_PRODUCT_SCHOOL'],
            'user' => $_ENV['STRIPE_PRODUCT_USER']
        ];

        $lineItems = [];

        foreach ($productIds as $key => $productId) {
            $price = \Stripe\Price::all([
                'product' => $productId,
                'limit' => 1,
            ])->data[0] ?? null;

            if (!$price) {
                continue;
            }

            // Base est toujours incluse
            if ($key === 'base') {
                $lineItems[] = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
                continue;
            }

            // Sinon, uniquement si une quantité est définie
            $qty = (int) ($quantities[$key] ?? 0);
            if ($qty > 0) {
                $lineItems[] = [
                    'price' => $price->id,
                    'quantity' => $qty,
                ];
            }
        }

        // Redirection vers Stripe Checkout
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => $lineItems,
            'subscription_data' => [
                'metadata' => ['user_id' => $user->getId()],
            ],
            'success_url' => $this->generateUrl('app_subscription_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_new_subscription', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }


    #[Route('/success', name: 'app_subscription_success')]
    public function success(): Response
    {
        $this->addFlash('success', 'Votre paiement a bien été reçu. Votre abonnement a mis à jour.');
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/show', name: 'app_subscription')]
    public function subscription(EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $subscriptions = $user->getSubscriptions(); // ou $em->getRepository(Invoice::class)->findBy(['owner' => $user])

        return $this->render('subscription/show.html.twig', [
            'user' => $user,
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/unsubscribe/{id}', name: 'app_unsubscribe', methods: ['POST'])]
    public function unsubscribe(EntityManagerInterface $em, Subscription $subscription): Response
    {
        if ($this->getUser() !== $subscription->getOwner()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet abonnement.');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            // 1. Récupérer l’abonnement
            \Stripe\Subscription::update($subscription->getStripeSubscriptionId(), [
                'cancel_at_period_end' => true,
            ]);

            // 3. Marquer l’utilisateur comme désabonné
            $subscription->setIsUnsubscribed(true);
            $em->flush();

            $this->addFlash('success', 'Votre abonnement a été résilié avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de la résiliation de votre abonnement : ' . $e->getMessage());
        }
        return $this->redirectToRoute('app_subscription');
    }

}
