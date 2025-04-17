<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            $email = (new \Symfony\Component\Mime\Email())
                ->from($data['email'])
                ->to('contact@qualiclass.com') // adapt à ton domaine
                ->subject('Message depuis QualiClass')
                ->text("Nom : {$data['name']}\n\nMessage : {$data['message']}");

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
        return $this->render('home/pricing.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
