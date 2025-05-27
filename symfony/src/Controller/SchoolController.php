<?php

namespace App\Controller;

use App\Form\SchoolType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/school')]
final class SchoolController extends AbstractController
{
    #[Route('/', name: 'app_school')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(SchoolType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $schoolImg = $form->get('schoolImg')->getData();
            if ($schoolImg) {
                $fileName = uniqid() . '.' . $schoolImg->guessExtension();
                $schoolImg->move($this->getParameter('school_directory'), $fileName);
                $user->setSchoolImg($fileName);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Les informations de l\'école ont été mises à jour avec succès.');
        }
        return $this->render('school/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
