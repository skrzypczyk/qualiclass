<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\Diploma;
use App\Entity\Module;
use App\Entity\ModuleCompetenceAffectation;
use App\Entity\Program;
use App\Form\CreateProgramType;
use App\Repository\ModuleRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/backoffice')]
final class ProgramController extends AbstractController
{
    #[Route('/program', name: 'app_program')]
    public function index(ProgramRepository $programRepository): Response
    {
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $programs = $programRepository->findBySchool($this->getUser()->getSchool());
        }else{
            $programs = $this->getUser()->getPrograms();
        }
        return $this->render('program/index.html.twig', [
            'controller_name' => 'ProgramController',
            'programs' => $programs,
        ]);
    }

    #[Route('/program/create', name: 'app_program_create')]
    #[Route('/program/edit/{id}', name: 'app_program_edit')]
    public function createEdit(Request $request, EntityManagerInterface $em, Program $program = null, ProgramRepository $programRepository): Response
    {
        if($program
            && $program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
            || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $create = false;
        if(!$program){
            $program = new Program();
            $program->setOwner($this->getUser());
            $create = true;
        }

        $form = $this->createForm(CreateProgramType::class, $program,[
            'user' => $this->getUser(),
        ]);

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $form->remove('owner');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $program = $form->getData();
            $em->persist($program);
            $em->flush();
            $this->addFlash('success', 'Programme enregistré avec succès !');
            return $this->redirectToRoute('app_program');
        }

        return $this->render('program/createEdit.html.twig', [
            'create' => $create,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/program/show/{id}', name: 'app_program_show')]
    public function show(Program $program, EntityManagerInterface $em): Response
    {
        if($program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas visualiser ce programme.');
            return $this->redirectToRoute('app_program');
        }


        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }


    #[Route('/program/{program}/diploma/{diploma}', name: 'app_program_diploma_show')]
    public function showByDiploma(Program $program, Diploma $diploma, EntityManagerInterface $em, Request $request, Environment $twig): Response
    {
        if($program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas visualiser ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $moduleByCompetence = [];
        $duration = 0;
        $credit = 0;

        foreach ($program->getAffectations() as $affectation) {
            $competence = $affectation->getCompetence();

            // On ne garde que les compétences liées au diplôme demandé
            if ($competence->getDiploma() === $diploma) {
                $competenceId = $competence->getId();

                if (!isset($moduleByCompetence[$competenceId])) {
                    $moduleByCompetence[$competenceId] = [
                        'duration' => 0,
                        'credit' => 0,
                        'competence' => $competence,
                        'modules' => [],
                    ];
                }

                $module = $affectation->getModule();

                // éviter les doublons
                if (!in_array($module, $moduleByCompetence[$competenceId]['modules'], true)) {
                    $moduleByCompetence[$competenceId]['modules'][] = $module;
                    $moduleByCompetence[$competenceId]['duration'] += $module->getDuration();
                    $moduleByCompetence[$competenceId]['credit'] += $module->getCredit();
                    $duration += $module->getDuration();
                    $credit += $module->getCredit();
                }
            }
        }

        $params = [
            'program' => $program,
            'diploma' => $diploma,
            'duration' => $duration,
            'credit' => $credit,
            'moduleByCompetence' => $moduleByCompetence,
            'user' => $this->getUser(),
        ];

        // Mode PDF ?
        if ($request->query->get('format') === 'pdf') {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // autorise l'accès aux URL HTTP/HTTPS
            $options->setChroot([$this->getParameter('kernel.project_dir') .'/public']);


            $dompdf = new Dompdf($options);
            $dompdf->setBasePath($this->getParameter('kernel.project_dir') . '/public');
            $html = $twig->render('program/showByDiploma.pdf.twig', $params);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return new Response(
                $dompdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $program->getTitle() . '-' . $diploma->getRNCP() . '.pdf"',
                    //'Content-Disposition' => 'attachment; filename="' . $program->getTitle() . '-' . $diploma->getRNCP() . '.pdf"',
                ]
            );
        }

        return $this->render('program/showByDiploma.html.twig', $params);
    }



    #[Route('/program/delete/{id}', name: 'app_program_delete')]
    public function delete(Program $program, EntityManagerInterface $em): Response
    {
        if($program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $em->remove($program);
        $em->flush();

        $this->addFlash('success', 'Programme supprimé avec succès.');
        return $this->redirectToRoute('app_program');
    }

    #[Route('/program/duplicate/{id}', name: 'app_program_duplicate')]
    public function duplicate(Program $program, EntityManagerInterface $em): Response
    {
        if($program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas dupliquer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        // Clone du programme
        $duplicateProgram = clone $program;
        $duplicateProgram->setTitle($program->getTitle() . ' (copie)');
        $duplicateProgram->setOwner($this->getUser());


        $em->persist($duplicateProgram);

        // Dupliquer chaque affectation
        foreach ($program->getAffectations() as $affectation) {
            $newAffectation = new ModuleCompetenceAffectation();
            $newAffectation->setProgram($duplicateProgram);
            $newAffectation->setModule($affectation->getModule());
            $newAffectation->setCompetence($affectation->getCompetence());
            $em->persist($newAffectation);
        }

        $em->flush();

        $this->addFlash('success', 'Programme dupliqué avec succès.');
        return $this->redirectToRoute('app_program');
    }


    #[Route('/program/assignment/{id}', name: 'app_program_assignment')]
    public function assignment(Request $request, Program $program, EntityManagerInterface $em, ModuleRepository $moduleRepository): Response
    {
        $user = $this->getUser();

        if($program->getOwner() !== $user
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas éditer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        // 1. Reconstituer les affectations existantes
        $existingMapping = [];

        foreach ($program->getAffectations() as $affectation) {
            $moduleId = $affectation->getModule()->getId();
            $competenceId = $affectation->getCompetence()->getId();

            if (!isset($existingMapping[$moduleId])) {
                $existingMapping[$moduleId] = [];
            }

            $existingMapping[$moduleId][] = $competenceId;
        }

        // 2. Traitement POST
        if ($request->isMethod('POST')) {
            $mapping = $request->request->all('mapping'); // [moduleId => [competenceId, ...]]

            // Supprimer les anciennes affectations
            foreach ($program->getAffectations() as $affectation) {
                $em->remove($affectation);
            }

            // Créer les nouvelles affectations
            foreach ($mapping as $moduleId => $competenceIds) {
                $module = $em->getRepository(Module::class)->find($moduleId);
                foreach ($competenceIds as $competenceId) {
                    $competence = $em->getRepository(Competence::class)->find($competenceId);
                    $affectation = new ModuleCompetenceAffectation();
                    $affectation->setProgram($program);
                    $affectation->setModule($module);
                    $affectation->setCompetence($competence);
                    $em->persist($affectation);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Affectations enregistrées avec succès.');
            return $this->redirectToRoute('app_program');
        }

        // 3. Tri personnalisé des modules
        //$modules = $user->getModules()->toArray();
        $modules = $moduleRepository->getModulesWithSharedAccess($user);

        $affectedModuleIds = array_keys($existingMapping);

        usort($modules, function (Module $a, Module $b) use ($affectedModuleIds) {

            // Affectés en premier
            $aAffected = in_array($a->getId(), $affectedModuleIds);
            $bAffected = in_array($b->getId(), $affectedModuleIds);

            if ($aAffected && !$bAffected) return -1;
            if (!$aAffected && $bAffected) return 1;

            // Archivés en dernier
            if ($a->isArchived() && !$b->isArchived()) return 1;
            if (!$a->isArchived() && $b->isArchived()) return -1;

            // Sinon tri alphabétique
            return strcmp($a->getTitle(), $b->getTitle());
        });

        return $this->render('program/assignement.html.twig', [
            'program' => $program,
            'modules' => $modules,
            'existingMapping' => $existingMapping,
        ]);
    }


}
