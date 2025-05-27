<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Subscription as AppSubscription;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Invoice as StripeInvoice;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        LoggerInterface $logger
    ): Response {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            $logger->error('Stripe webhook error: ' . $e->getMessage());
            return new Response('Invalid signature', 400);
        }

        if ($event->type === 'invoice.paid') {
            return $this->handleInvoicePaid($event->data->object, $em, $userRepository, $logger);
        }

        return new JsonResponse(['status' => 'ignored']);
    }

    private function handleInvoicePaid(
        StripeInvoice $invoiceData,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        LoggerInterface $logger,

    ): Response {
        $stripeInvoiceId = $invoiceData->id;
        $subscriptionId = $invoiceData->subscription;
        $customerId = $invoiceData->customer;
        $amount = $invoiceData->amount_paid;
        $currency = strtoupper($invoiceData->currency);
        $paidAt = $invoiceData->status_transitions->paid_at ?? time();

        // Vérifie doublon
        $existingInvoice = $em->getRepository(Invoice::class)->findOneBy([
            'stripeInvoiceId' => $stripeInvoiceId,
        ]);

        if ($existingInvoice) {
            return new JsonResponse(['status' => 'invoice already exists']);
        }

        $stripeSub = Subscription::retrieve($subscriptionId);
        $userId = $stripeSub->metadata->user_id ?? null;

        if (!$userId) {
            $logger->warning('user_id manquant dans metadata');
            return new JsonResponse(['error' => 'Missing user_id in metadata'], 400);
        }

        $user = $userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $limits = $this->extractLimitsFromSubscription($stripeSub);

        $subscription = $this->findOrCreateSubscription(
            $em,
            $subscriptionId,
            $customerId,
            $user,
            $limits
        );

        $this->adjustLimits($subscription,$user, $em);

        $invoice = (new Invoice())
            ->setStripeInvoiceId($stripeInvoiceId)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setPaidAt((new \DateTimeImmutable())->setTimestamp($paidAt))
            ->setInvoiceUrl($invoiceData->hosted_invoice_url ?? null)
            ->setSubscription($subscription);

        $em->persist($invoice);
        $em->flush();

        return new JsonResponse(['status' => 'invoice saved']);
    }

    private function extractLimitsFromSubscription(Subscription $stripeSub): array
    {
        $limits = [
            'school' => 0,
            'user' => 0,
        ];

        foreach ($stripeSub->items->data as $item) {
            $productId = $item->price->product;
            $qty = $item->quantity ?? 0;

            if ($productId === $_ENV['STRIPE_PRODUCT_SCHOOL']) {
                $limits['school'] = $qty;
            }
            if ($productId === $_ENV['STRIPE_PRODUCT_USER']) {
                $limits['user'] = $qty;
            }
        }

        return $limits;
    }

    private function findOrCreateSubscription(
        EntityManagerInterface $em,
        string $stripeSubId,
        string $customerId,
                               $user,
        array $limits
    ): AppSubscription {
        $subscription = $em->getRepository(AppSubscription::class)->findOneBy([
            'stripeSubscriptionId' => $stripeSubId,
        ]);

        if (!$subscription) {
            $subscription = new AppSubscription();
            $subscription->setStripeSubscriptionId($stripeSubId);
            $subscription->setStripeCustomerId($customerId);
            $subscription->setOwner($user);
        }

        $subscription->setLimitSchools($limits['school']);
        $subscription->setLimitUsers($limits['user']);

        $em->persist($subscription);

        return $subscription;
    }

    function adjustLimits(\App\Entity\Subscription $subscription, User $user, EntityManagerInterface $em): void
    {
        $limitSchools = $user->getLimitSchools() ?? $subscription->getLimitSchools(true);
        $schools = $user->getSchools(true)->toArray(); // true = toutes les écoles, actives ou non

        if (count($schools) > $limitSchools) {
            // On trie pour garder les plus anciennes actives
            $activeSchools = array_filter($schools, fn($school) => !$school->isDisable());
            $toDisable = array_slice($activeSchools, $limitSchools); // celles à désactiver

            foreach ($toDisable as $school) {
                $school->setIsDisable(true);
                $em->persist($school);
            }
            $em->flush();
        }

        $limitUsers = $user->getLimitUsers() ?? $subscription->getLimitUsers(true);
        $users = $user->getUsers(true)->toArray(); // true = toutes les écoles, actives ou non

        if (count($users) > $limitUsers) {
            // On trie pour garder les plus anciennes actives
            $activeUsers = array_filter($users, fn($user) => !$user->isDisable());
            $toDisable = array_slice($activeUsers, $limitUsers); // celles à désactiver

            foreach ($toDisable as $user) {
                $user->setIsDisable(true);
                $em->persist($user);
            }
            $em->flush();
        }


    }
}
