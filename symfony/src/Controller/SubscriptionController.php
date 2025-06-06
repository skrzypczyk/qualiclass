<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Service\StripeService;
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
    public function new(StripeService $stripeService): Response
    {
        $products = $stripeService->getProductsWithPrices();

        return $this->render('subscription/new.html.twig', [
            'products' => $products,
        ]);
    }



    #[Route('/create', name: 'app_subscription_store', methods: ['POST'])]
    public function create(
        Request $request,
        StripeService $stripeService
    ): Response {
        $products = $stripeService->getProductsWithPrices();
        $lineItems = $stripeService->getLineItemsFromRequest($request, $products);
        return $this->redirect($stripeService->getCreateUrl($lineItems, $this->getUser()->getId()), 303);
    }


    #[Route('/change/{id}', name: 'app_change_subscription', methods: ['GET'])]
    public function change(
        Request $request,
        Subscription $subscription,
        StripeService $stripeService
    ): Response {

        if ($this->getUser()->getSchool() !== $subscription->getSchool()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet abonnement.');
        }

        return $this->render('subscription/update.html.twig', [
            'products' => $stripeService->getProductsWithPrices(),
            'subscription' => $subscription,
        ]);
    }


    #[Route('/update/{id}', name: 'app_update_subscription', methods: ['POST'])]
    public function update(
        Request $request,
        Subscription $subscription,
        StripeService $stripeService
    ): Response {
        if ($this->getUser()->getSchool() !== $subscription->getSchool()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet abonnement.');
        }
        $products = $stripeService->getProductsWithPrices();
        $lineItems = $stripeService->getLineItemsFromRequest($request, $products);
        try {
            $stripeService->updateSubscriptionItems($subscription->getStripeSubscriptionId(), $lineItems);$this->addFlash('success', 'Votre abonnement a été mis à jour avec succès.');
            $this->addFlash('success', 'Votre abonnement a été mis à jour avec succès.');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Erreur lors de la mise à jour de l’abonnement : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_subscription');
    }




    #[Route('/success', name: 'app_subscription_success')]
    public function success(): Response
    {
        $this->addFlash('success', 'Votre paiement a bien été reçu. Votre abonnement a mis à jour.');
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/show', name: 'app_subscription')]
    public function subscription(EntityManagerInterface $em, StripeService $stripeService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $subscriptions = $user->getSchool()->getSubscriptions(); // ou $em->getRepository(Invoice::class)->findBy(['owner' => $user])
        foreach ($subscriptions as $subscription)
        {
            $invoicesUpcoming[$subscription->GetId()] = $stripeService->getNextInvoice($subscription->getStripeCustomerId(), $subscription->getStripeSubscriptionId());
        }

        return $this->render('subscription/show.html.twig', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'invoicesUpcoming' => $invoicesUpcoming ?? [],
        ]);
    }

    #[Route('/unsubscribe/{id}', name: 'app_unsubscribe', methods: ['POST'])]
    public function unsubscribe(EntityManagerInterface $em, Subscription $subscription, StripeService $stripeService): Response
    {
        if ($this->getUser()->getSchool() !== $subscription->getSchool()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet abonnement.');
        }
        try {
            $stripeService->unsubscribe($subscription->getStripeSubscriptionId());
            $subscription->setIsUnsubscribed(true);
            $em->persist($subscription);
            $em->flush();
            $this->addFlash('success', 'Votre abonnement a été résilié avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de la résiliation de votre abonnement : ' . $e->getMessage());
        }
        return $this->redirectToRoute('app_subscription');
    }

}
