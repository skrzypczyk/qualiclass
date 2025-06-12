<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_ADMIN']);
            $school = new School();
            $school->setName($form->get('school')->getData());
            $user->setSchool($school);
            $entityManager->persist($user);
            $entityManager->flush();


            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@qualiclass.com', 'QualiClass'))
                    ->to((string) $user->getEmail())
                    ->subject('Veuillez confirmer votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'user' => $user,
                    ])
            );

            // 🔔 ENVOI DE L'EMAIL DE NOTIFICATION INTERNE
            $adminNotification = (new TemplatedEmail())
                ->from(new Address('contact@qualiclass.com', 'QualiClass'))
                ->to('contact@qualiclass.com') // remplace par ton adresse réelle
                ->subject('Nouveau compte créé sur QualiClass')
                ->htmlTemplate('registration/admin_new_account.html.twig')
                ->context([
                    'user' => $user,
                    'school' => $school,
                ]);
            $mailer->send($adminNotification);

            $this->addFlash('success', 'Veuillez activer votre compte en cliquant sur le lien que nous vous avons envoyé par email.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email/{id}', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, Uuid $id, TranslatorInterface $translator, EntityManagerInterface $em): Response
    {
        try {
            $user = $em->getRepository(User::class)->find($id);
            if (!$user) {
                throw new \LogicException('Utilisateur non trouvé.');
            }

            $this->emailVerifier->handleEmailConfirmation($request, $user);

            $this->addFlash('success', 'Votre adresse email a bien été vérifiée.');
            return $this->redirectToRoute('app_login');

        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }
    }
    #[Route('/resend-confirmation', name: 'app_resend_confirmation')]
    public function resendConfirmation(
        Request $request,
        EntityManagerInterface $em,
        EmailVerifier $emailVerifier,
    ): Response {
        $email = $request->request->get('email');

        if ($request->isMethod('POST') && $email) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user && !$user->isVerified()) {
                $emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('contact@qualiclass.com', 'QualiClass'))
                        ->to($user->getEmail())
                        ->subject('Nouveau lien de confirmation')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'user' => $user,
                        ])
                );
            }

            $this->addFlash('success', 'Si un compte existe pour cet email et n’est pas encore activé, un nouveau lien a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/resend_confirmation.html.twig');
    }


}
