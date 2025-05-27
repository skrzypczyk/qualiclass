<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/backoffice')]
final class ProfilController extends AbstractController
{

    public function __construct(private EmailVerifier $emailVerifier)
    {

    }

    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        dump((string) $form->getErrors(true, false));


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    password_hash($form->get('plainPassword')->getData(), PASSWORD_BCRYPT)
                );
            }
            if(!is_null($form->get('email')->getData())) {
                $user->setEmail($form->get('email')->getData());
                $user->setIsVerified(false);
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('contact@qualiclass.fr', 'QualiClass'))
                        ->to((string) $user->getEmail())
                        ->subject('Veuillez confirmer votre adresse email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'user' => $user,
                        ])
                );
                $this->addFlash('success', 'Veuillez activer votre compte en cliquant sur le lien que nous vous avons envoyé par email.');
                $em->flush();
                // Invalide la session
                $session->invalidate();
                // Redirige vers la route de logout
                return $this->redirectToRoute('app_logout');
            }

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            $em->flush();
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

}
