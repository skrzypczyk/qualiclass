<?php
namespace App\Service;

use App\Entity\Invoice;
use App\Entity\Subscription;
use App\Repository\InvoiceRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;
use App\Service\StripeService;

class StripeWebhookHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private UserRepository $userRepository,
        private SubscriptionRepository $subscriptionRepository,
        private InvoiceRepository $invoiceRepository,
        private StripeService $stripeService
    ) {}

    public function handle(Event $event): void
    {
        try {
            match ($event->type) {
                'customer.subscription.created' => $this->handleSubscriptionCreated($event),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
                'invoice.paid' => $this->handleInvoicePaid($event),
                default => null,
            };
        } catch (\Throwable $e) {
            $this->logger->error('Erreur webhook Stripe : ' . $e->getMessage());
        }
    }

    private function handleSubscriptionCreated(Event $event): void
    {
        $this->createSubscriptionFromStripeData($event->data->object);
    }

    private function handleSubscriptionUpdated(Event $event): void
    {
        $subscription = $event->data->object;

        $sub = $this->subscriptionRepository->findOneBy(["stripeSubscriptionId" => $subscription->id]);
        if (!$sub) return;

        // Exemple : mise à jour des quotas ou autres options
        $sub->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    private function handleSubscriptionDeleted(Event $event): void
    {
        $subscription = $event->data->object;
        $sub = $this->subscriptionRepository->findOneBy(["stripeSubscriptionId" => $subscription->id]);
        if (!$sub) return;
        $sub->setCanceledAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    private function handleInvoicePaid(Event $event): void
    {
        $invoice = $event->data->object;
        $stripeSubId = $invoice->subscription;
        $sub = $this->subscriptionRepository->findOneBy(["stripeSubscriptionId" => $stripeSubId]);
        if (!$sub) {
            $this->logger->warning("Abonnement introuvable pour invoice.paid : $stripeSubId — tentative de création depuis Stripe.");
            $stripeSub = $this->stripeService->retrieveSubscription($stripeSubId);
            $sub = $this->createSubscriptionFromStripeData($stripeSub);
            if (!$sub) {
                throw new \Exception("Impossible de créer l’abonnement manquant pour $stripeSubId.");
            }
        }

        // Vérifie si la facture a déjà été enregistrée
        $existingInvoice = $this->invoiceRepository->findOneBy(['stripeInvoiceId' => $invoice->id]);
        if ($existingInvoice) {
            return;
        }

        // Enregistre la facture
        $newInvoice = new Invoice();
        $newInvoice->setStripeInvoiceId($invoice->id);
        $newInvoice->setAmount($invoice->amount_paid); // En centimes
        $newInvoice->setCurrency($invoice->currency);
        $newInvoice->setPaidAt((new \DateTimeImmutable())->setTimestamp($invoice->status_transitions->paid_at ?? time()));
        $newInvoice->setInvoiceUrl($invoice->hosted_invoice_url ?? null);
        $newInvoice->setSubscription($sub);
        $this->em->persist($newInvoice);

        // Met à jour les options de l’abonnement à partir des items Stripe

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $stripeSub = \Stripe\Subscription::retrieve($stripeSubId);

        foreach ($stripeSub->items->data as $item) {
            $productId = $item->price->product;
            $quantity = $item->quantity ?? 1;

            if ($productId === $_ENV['STRIPE_PRODUCT_USER']) {
                $sub->setLimitUsers($quantity);
            }

            if ($productId === $_ENV['STRIPE_PRODUCT_GPT']) {
                $sub->setChatgpt($quantity > 0);
            }
        }

        $this->em->flush();
        $this->adjustLimits($sub);

    }



    function adjustLimits(Subscription $subscription): void
    {
        $school = $subscription->getSchool();
        $limitUsers = $school->getLimitUsers() ?? $subscription->getLimitUsers(true);
        $users = $school->getUsers(true)->toArray(); // true = toutes les écoles, actives ou non
        if (count($users) > $limitUsers) {
            // On trie pour garder les plus anciennes actives
            $activeUsers = array_filter($users, fn($user) => !$user->isDisable());
            $toDisable = array_slice($activeUsers, $limitUsers); // celles à désactiver

            foreach ($toDisable as $user) {
                $user->setIsDisable(true);
                $this->em->persist($user);
            }
            $this->em->flush();
        }
    }

    private function createSubscriptionFromStripeData(object $stripeSubscription): ?Subscription
    {

        $userId = $stripeSubscription->metadata->user_id ?? null;
        if (!$userId) {
            $this->logger->warning("Impossible de créer une souscription : user_id manquant dans les métadonnées.");
            return null;
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            $this->logger->warning("Utilisateur introuvable pour user_id={$userId}.");
            return null;
        }

        $subscription = new Subscription();
        $subscription->setStripeSubscriptionId($stripeSubscription->id);
        $subscription->setSchool($user->getSchool());
        $subscription->setStripeCustomerId($stripeSubscription->customer);
        $subscription->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }


}
