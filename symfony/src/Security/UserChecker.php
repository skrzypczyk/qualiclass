<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        // Check if the user is super admin

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Veuillez valider votre adresse email.');
        }


        else if ($user->isDisable()) {
            throw new CustomUserMessageAccountStatusException('Votre compte a été désactivé, veuillez contacter votre référent.');
        }

        if ($user->isSuperAdmin() ||$user->isAdmin()) {
            return;
        }

        $school = $user->getSchool();
        if ($school && !$school->getLastInvoiceValid()) {
            throw new CustomUserMessageAccountStatusException('Le compte de votre référent n\'est pas validé.');
        }

    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
