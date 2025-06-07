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

class StripeWebhookHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private UserRepository $userRepository,
        private SubscriptionRepository $subscriptionRepository,
        private InvoiceRepository $invoiceRepository
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
        $subscription = $event->data->object;
        $userId = $subscription->metadata->user_id ?? null;

        if ($userId) {
            $user = $this->userRepository->find($userId);
        }else{
            return;
        }

        $newSub = new Subscription();
        $newSub->setStripeSubscriptionId($subscription->id);
        $newSub->setSchool($user->getSchool());
        $newSub->setStripeCustomerId($subscription->customer);
        $newSub->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($newSub);
        $this->em->flush();
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
            $this->logger->warning("stripe_invoice - Abonnement introuvable pour invoice.paid : $stripeSubId. Tentative de création à partir de l’objet Stripe.");

            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $stripeSub = Subscription::retrieve($stripeSubId);

            // Création d’un faux Event pour appeler handleSubscriptionCreated
            $fakeEvent = new Event();
            $fakeEvent->type = 'customer.subscription.created';
            $fakeEvent->data = new \stdClass();
            $fakeEvent->data->object = $stripeSub;

            $this->handleSubscriptionCreated($fakeEvent);

            // On tente à nouveau de récupérer la subscription après sa création
            $sub = $this->subscriptionRepository->findOneBy(["stripeSubscriptionId" => $stripeSubId]);

            if (!$sub) {
                $this->logger->critical("stripe_invoice - Échec de la création d’un abonnement Stripe manquant : $stripeSubId");
                return;
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

}
