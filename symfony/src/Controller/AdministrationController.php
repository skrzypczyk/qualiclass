<?php

namespace App\Controller;

use App\Form\UserEditAdminType;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

#[Route('/admin')]
final class AdministrationController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $allUsers = $userRepository->findAll();

        $users = array_filter($allUsers, function ($user) {
            return in_array('ROLE_ADMIN', $user->getRoles(), true)
                || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
        });

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'users' => $users
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
    public function editUser(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $school = $this->getUser()->getSchool();
        $form = $this->createForm(UserEditAdminType::class, $user, [
            'isFreeAccount' => $school->isFreeAccount(),
            'limitUsers' => $school->getLimitUsers(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }
            $school->setLimitUsers($form->get('limitUsers')->getData());
            $school->setIsFreeAccount($form->get('isFreeAccount')->getData());

            $em->flush();
            $this->addFlash('success', 'Utilisateur mis à jour.');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('administration/editUser.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
