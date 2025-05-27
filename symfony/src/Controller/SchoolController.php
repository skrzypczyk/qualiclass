<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\CreateSchoolType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/backoffice/school')]
final class SchoolController extends AbstractController
{
    #[Route('/', name: 'app_school')]
    #[Route('/{id}', name: 'school_edit')]
    public function index(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, School $school=null): Response
    {
        $subscription = $this->getUser()->getLastSubscription();
        $limitSchools = $this->getUser()->getLimitSchools() ?? $subscription->getLimitSchools(true);

        if (is_null($school)) {
            $school = new School();
        }else {
            if ($school->getOwner() !== $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier cette école.');
                return $this->redirectToRoute('app_school');
            }
        }

        $form = $this->createForm(CreateSchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var School $school */
            $school = $form->getData();
            $logoFile = $form['logo']->getData();

            if ($logoFile) {
                $uuid = Uuid::v4()->toRfc4122();
                $schoolDir = $this->getParameter('kernel.project_dir') . '/public/uploads/schools/';
                if (!file_exists($schoolDir)) {
                    mkdir($schoolDir, 0775, true);
                }
                $newFilename = $uuid .".". $logoFile->guessExtension();
                $logoFile->move($schoolDir, $newFilename);
                $school->setLogo('uploads/schools/'. $newFilename);
            }

            $school->setOwner($this->getUser());
            $em->persist($school);
            $em->flush();

            $this->addFlash('success', 'École ajoutée avec succès.');
            return $this->redirectToRoute('app_school');
        }

        return $this->render('school/index.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'subscription' => $subscription,
            'limitSchools' => $limitSchools,
            'school' => $school,
        ]);
    }

    #[Route('/delete/{id}', name: 'school_delete')]
    public function delete(School $school, EntityManagerInterface $em): Response
    {
        if($school->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette école.');
            return $this->redirectToRoute('app_school');
        }

        $em->remove($school);
        $em->flush();

        $this->addFlash('success', 'École supprimée avec succès.');
        return $this->redirectToRoute('app_school');
    }
    #[Route('/status/{id}', name: 'school_status')]
    public function status(School $school, EntityManagerInterface $em): Response
    {
        if($school->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette école.');
            return $this->redirectToRoute('app_school');
        }

        $subscription = $this->getUser()->getLastSubscription();
        $limitSchools = $this->getUser()->getLimitSchools() ?? $subscription->getLimitSchools(true);
        $schools = $this->getUser()->getSchools(true);

        if ($school->isDisable()) {
            if (count($schools) >= $limitSchools) {
                $this->addFlash('error', 'Vous ne pouvez pas activer cette école car vous avez atteint la limite de '. $limitSchools . ' écoles.');
                return $this->redirectToRoute('app_school');
            }
        }


        $school->setIsDisable(!$school->isDisable());
        $em->persist($school);
        $em->flush();

        $this->addFlash('success', 'École '.($school->isDisable() ? 'désactivée' : 'activée' ). ' avec succès.');
        return $this->redirectToRoute('app_school');
    }
}
