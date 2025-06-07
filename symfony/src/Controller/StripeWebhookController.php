<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Subscription as AppSubscription;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\StripeWebhookHandler;
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
        StripeWebhookHandler $handler
    ): Response {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->headers->get('stripe-signature');
            $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

            $event = Webhook::constructEvent($payload, $sigHeader, $secret);

            $handler->handle($event);

            return new JsonResponse(['status' => 'ok']);
        } catch (\Throwable $e) {
            // Log obligatoire si tu veux suivre les erreurs
            $this->get('logger')->error('[Stripe Webhook] ' . $e->getMessage());

            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
