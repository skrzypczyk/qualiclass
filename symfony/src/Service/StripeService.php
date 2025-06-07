<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Checkout\Session;
use Stripe\Subscription;
use Stripe\Invoice;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{


    public function __construct(
        private string $stripeSecretKey,
        private array $productIds,
        private UrlGeneratorInterface $urlGenerator
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Récupère les informations produit + prix depuis Stripe.
     */
    public function getProductsWithPrices(): array
    {
        $products = [];

        foreach ($this->productIds as $key => $productId) {
            $product = Product::retrieve($productId);
            $prices = Price::all(['product' => $productId, 'limit' => 1]);

            $price = $prices->data[0] ?? null;


            $products[$key] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price_id' => $price?->id,
                'price_amount' => $price ? $price->unit_amount / 100  : 0,
                'currency' => $price?->currency ?? 'eur',
                'recurring' => $price?->recurring?->interval ?? null,
            ];
        }

        return $products;
    }

    public function getLineItemsFromRequest($request, $products)
    {
        $quantities = $request->request->all('quantities');
        $lineItems = [];

        foreach ($this->productIds as $key => $productId) {
            $price = $products[$key]['price_id'] ?? null;
            if (!$price)continue;

            if($key == "base"){
                $lineItems[] = [
                    'price' => $price,
                    'quantity' => 1,
                ];
            }elseif($key == "gpt" && isset($quantities[$key])){
                $lineItems[] = [
                    'price' => $price,
                    'quantity' => 1,
                ];
            }elseif((int)($quantities[$key] ?? 0) > 0){
                $lineItems[] = [
                    'price' => $price,
                    'quantity' => (int)($quantities[$key] ?? 0),
                ];
            }

        }
        return $lineItems;
    }

    public function unsubscribe(string $stripeSubscriptionId): void
    {
        try {
            Subscription::update($stripeSubscriptionId, [
                'cancel_at_period_end' => true,
            ]);
        } catch (\Exception $e) {
            // Log or handle the error as needed
            throw new \RuntimeException('Failed to unsubscribe: ' . $e->getMessage());
        }
    }

    public function getCreateUrl(array $lineItems, int $userId): string
    {
        return  Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => $lineItems,
            'subscription_data' => [
                'metadata' => ['user_id' => $userId],
            ],
            'success_url' => $this->urlGenerator->generate('app_subscription_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_new_subscription', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ])->url;
    }

    public function getNextInvoice(string $customerId, string $subscriptionId): ?array
    {
        try {
            $preview = Invoice::createPreview([
                'customer' => $customerId,
                'subscription' => $subscriptionId,
            ]);

            return $preview->toArray();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Cas où il n'y a pas de prochaine facture
            if (str_contains($e->getMessage(), 'No upcoming invoices')) {
                return null;
            }
            throw new \RuntimeException("Erreur Stripe : " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException("Erreur lors de la récupération de la prochaine facture : " . $e->getMessage());
        }
    }


    public function updateSubscriptionItems(string $subscriptionId, array $lineItems): void
    {
        $subscription = Subscription::retrieve($subscriptionId);
        $existingItems = $subscription->items->data;

        $itemsToUpdate = [];
        $lineItemPriceIds = array_column($lineItems, 'price');

        // Map des quantités par price ID pour calcul du total
        $existingQuantities = [];
        foreach ($existingItems as $item) {
            $existingQuantities[$item->price->id] = $item->quantity ?? 0;
        }

        $newQuantities = [];
        foreach ($lineItems as $item) {
            $newQuantities[$item['price']] = $item['quantity'] ?? 0;
        }

        // Calcul du montant actuel et futur
        $currentTotal = 0;
        $newTotal = 0;

        foreach ($existingItems as $item) {
            $unitAmount = $item->price->unit_amount ?? 0;
            $quantity = $item->quantity ?? 0;
            $currentTotal += $unitAmount * $quantity;
        }

        foreach ($lineItems as $item) {
            // On peut faire un appel à Price::retrieve si tu ne l’as pas déjà
            $price = \Stripe\Price::retrieve($item['price']);
            $newTotal += $price->unit_amount * $item['quantity'];
        }

        // Choix du comportement
        $prorationBehavior = $newTotal > $currentTotal ? 'always_invoice' : 'none';

        // Préparation des items à mettre à jour
        foreach ($lineItems as $lineItem) {
            $existingItem = null;
            foreach ($existingItems as $subItem) {
                if ($subItem->price->id === $lineItem['price']) {
                    $existingItem = $subItem;
                    break;
                }
            }

            if ($existingItem) {
                $itemsToUpdate[] = [
                    'id' => $existingItem->id,
                    'quantity' => $lineItem['quantity'],
                ];
            } else {
                $itemsToUpdate[] = [
                    'price' => $lineItem['price'],
                    'quantity' => $lineItem['quantity'],
                ];
            }
        }

        // Suppression des anciens items non inclus
        foreach ($existingItems as $subItem) {
            if (!in_array($subItem->price->id, $lineItemPriceIds, true)) {
                $itemsToUpdate[] = [
                    'id' => $subItem->id,
                    'deleted' => true,
                ];
            }
        }

        Subscription::update($subscriptionId, [
            'items' => $itemsToUpdate,
            'proration_behavior' => $prorationBehavior,
        ]);
    }

    public function retrieveSubscription(string $subscriptionId): ?\Stripe\Subscription
    {
        try {
            return Subscription::retrieve($subscriptionId);
        } catch (\Exception $e) {
            throw new \RuntimeException("Impossible de récupérer l'abonnement Stripe $subscriptionId : " . $e->getMessage());
        }
    }


}
