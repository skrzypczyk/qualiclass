<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/backoffice/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    #[Route('/{id}', name: 'user_edit')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        User $user=null,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailerInterface $mailer): Response
    {
        $subscription = $this->getUser()->getLastSubscription();
        $limitUsers = $this->getUser()->getLimitUsers() ?? $subscription->getLimitUsers(true);

        $isNewUser = false;
        if (is_null($user)) {
            $user = new User();
            $isNewUser = true;
        }else {
            if ($user->getOwner() !== $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier cet utilisateur.');
                return $this->redirectToRoute('app_user');
            }
        }

        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setOwner($this->getUser());
            $user->setIsVerified(true);
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();


            $resetToken = $resetPasswordHelper->generateResetToken($user);
            $email = (new TemplatedEmail())
                ->from(new Address('contact@qualiclass.com', 'QualiClass'))
                ->to($user->getEmail())
                ->subject('Bienvenue sur QualiClass – Définissez votre mot de passe')
                ->htmlTemplate('user/email.html.twig')
                ->context([
                    'resetTokenUrl' => $this->generateUrl('app_reset_password', [
                        'token' => $resetToken->getToken()
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    'user' => $this->getUser()
                ]);
            $mailer->send($email);
            if ($isNewUser) {
                $this->addFlash('success', 'Utilisateur ajouté avec succès, un mail vient de lui être envoyé afin de personnaliser son mot de passe.');
            } else {
                $this->addFlash('success', 'Utilisateur modifié avec succès, un mail vient de lui être envoyé afin de personnaliser son mot de passe.');
            }
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
            'limitUsers'=>$limitUsers,
            'subscription' => $subscription,
            'user'=>$user,
            'userConnected'=>$this->getUser(),
        ]);
    }

    #[Route('/status/{id}', name: 'user_status')]
    public function status(User $user, EntityManagerInterface $em): Response
    {
        if($user->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet utilisateur.');
            return $this->redirectToRoute('app_user');
        }

        $subscription = $this->getUser()->getLastSubscription();
        $limitUsers = $this->getUser()->getLimitUsers() ?? $subscription->getLimitUsers(true);
        $users = $this->getUser()->getUsers(true);

        if ($user->isDisable()) {
            if (count($users) >= $limitUsers) {
                $this->addFlash('error', 'Vous ne pouvez pas activer cet utilisateur car vous avez atteint la limite de '. $limitUsers . ' utilisateurs.');
                return $this->redirectToRoute('app_user');
            }
        }


        $user->setIsDisable(!$user->isDisable());
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur '.($user->isDisable() ? 'désactivé' : 'activé' ). ' avec succès.');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/delete/{id}', name: 'user_delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        if($user->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet utilisateur.');
            return $this->redirectToRoute('app_user');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('app_user');
    }
}
