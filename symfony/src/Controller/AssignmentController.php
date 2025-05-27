<?php

namespace App\Controller;

use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/assignment')]
class AssignmentController extends AbstractController
{
    #[Route('/', name: 'app_assignment')]
    public function index(Request $request, EntityManagerInterface $em, UserRepository $userRepository, SchoolRepository $schoolRepository): Response
    {
        $users = $userRepository->findSelfAndOwnedUsers($this->getUser());

        $schools = $schoolRepository->findBy([
            'isDisable' => false,
            'owner' => $this->getUser(),
        ]);

        return $this->render('assignment/index.html.twig', [
            'users' => $users,
            'schools' => $schools,
        ]);
    }

    #[Route('/update', name: 'app_assignment_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $em, UserRepository $userRepository, SchoolRepository $schoolRepository): Response
    {
        $data = $request->request->all('assignments', []);

        $users = $userRepository->findSelfAndOwnedUsers($this->getUser());

        foreach ($users as $user) {
            $selectedSchoolIds = $data[$user->getId()] ?? [];

            // On nettoie tout
            foreach ($user->getSchool() as $school) {
                if (!in_array($school->getId(), $selectedSchoolIds)) {
                    $user->getSchool()->removeElement($school);
                }
            }

            // On ajoute les nouvelles
            foreach ($selectedSchoolIds as $schoolId) {
                $school = $schoolRepository->find($schoolId);
                if ($school && !$user->getSchool()->contains($school)) {
                    $user->getSchool()->add($school);
                }
            }
        }

        $em->flush();

        $this->addFlash('success', 'Affectations mises à jour avec succès.');
        return $this->redirectToRoute('app_assignment');
    }

}
