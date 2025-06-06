<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\StripeService;
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

    #[Route('/privacy_policy', name: 'app_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('home/privacyPolicy.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/pricing', name: 'app_pricing')]
    public function pricing(StripeService $stripeService): Response
    {
        $products = $stripeService->getProductsWithPrices();
        return $this->render('home/pricing.html.twig', [
            'products' => $products,
        ]);
    }
}
