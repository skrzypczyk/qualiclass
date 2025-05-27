<?php

namespace App\Controller;

use App\Entity\Assessment;
use App\Form\CreateAssessmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/assessment')]
final class AssessmentController extends AbstractController
{
    #[Route('/', name: 'app_assessment')]
    #[Route('/edit/{id}', name: 'app_edit_assessment')]
    public function index( Request $request, EntityManagerInterface $em, Assessment $assessment = null): Response
    {

        $create = false;
        if(!$assessment){
            $assessment = new Assessment();
            $create = true;
        }else if($assessment->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette évaluation.');
            return $this->redirectToRoute('app_assessment');
        }

        $form = $this->createForm(CreateAssessmentType::class, $assessment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $assessment = $form->getData();
            $assessment->setOwner($this->getUser());
            $em->persist($assessment);
            $em->flush();
            $this->addFlash('success', 'Evalution enregistrée avec succès !');
            return $this->redirectToRoute('app_assessment');
        }

        return $this->render('assessment/index.html.twig', [
            'create' => $create,
            'form' => $form->createView(),
            'assessments'=>$this->getUser()->getAssessments(),
        ]);
    }



    #[Route('/delete/{id}', name: 'assessment_delete')]
    public function delete(Assessment $assessment, EntityManagerInterface $em): Response
    {
        if($assessment->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette évaluation.');
            return $this->redirectToRoute('app_assessment');
        }

        $em->remove($assessment);
        $em->flush();

        $this->addFlash('success', 'Evaluation supprimée avec succès.');
        return $this->redirectToRoute('app_assessment');
    }
}
