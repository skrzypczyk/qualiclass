<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, \Symfony\Component\Mailer\MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // 👉 Exemple simple d'envoi avec Mailer
            $email = (new Email())
                ->from(new Address('contact@qualiclass.com', 'QualiClass'))
                ->to(new Address('y.skrzypczyk@gmail.com', 'Yves SKRZYPCZYK'))
                ->subject('Message depuis QualiClass')
                ->text("Nom : {$data['name']}\n\nEmail: {$data['email']} \n\nMessage : {$data['message']}");

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('home/cgu.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/pricing', name: 'app_pricing')]
    public function pricing(): Response
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Liste des ID des produits Stripe
        $productIds = [
            'base' => $_ENV['STRIPE_PRODUCT_BASE'],
            'user' => $_ENV['STRIPE_PRODUCT_USER'],
        ];

        $products = [];

        foreach ($productIds as $productId) {
            $product = \Stripe\Product::retrieve($productId);
            $prices = \Stripe\Price::all(['product' => $productId, 'limit' => 1]);

            $products[] = [
                'name' => $product->name,
                'description' => $product->description ?? '',
                'price' => $prices->data[0]?->unit_amount ?? null,
                'currency' => $prices->data[0]?->currency ?? 'eur',
                'interval' => $prices->data[0]?->recurring->interval ?? null,
            ];
        }

        return $this->render('home/pricing.html.twig', [
            'products' => $products,
        ]);
    }
}
