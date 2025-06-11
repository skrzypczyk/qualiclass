<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Competence;
use App\Entity\Credit;
use App\Entity\Diploma;
use App\Entity\Module;
use App\Entity\ModuleCompetenceAssignment;
use App\Entity\Program;
use App\Form\CreateProgramType;
use App\Repository\AssignmentRepository;
use App\Repository\CompetenceRepository;
use App\Repository\CreditRepository;
use App\Repository\ModuleCompetenceAssignmentRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProgramRepository;
use App\Repository\SettingRepository;
use App\Service\ChatGptClient;
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
    public function show(Program $program, EntityManagerInterface $em, Request $request,Environment $twig): Response
    {
        if($program->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas visualiser ce programme.');
            return $this->redirectToRoute('app_program');
        }
        $assignments = $program->getGroupedAssignments(); // ou $assignmentRepository->findBy(['program' => $program])

        $params = [
            'program' => $program,
            'programGroupedAssignments' => $assignments,
            'user' => $this->getUser()
        ];


        if ($request->query->get('format') === 'pdf') {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // autorise l'accès aux URL HTTP/HTTPS
            $options->setChroot([$this->getParameter('kernel.project_dir') .'/public']);


            $dompdf = new Dompdf($options);
            $dompdf->setBasePath($this->getParameter('kernel.project_dir') . '/public');
            $html = $twig->render('program/showProgram.pdf.twig', $params);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return new Response(
                $dompdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $program->getTitle() . '.pdf"',
                    //'Content-Disposition' => 'attachment; filename="' . $program->getTitle() . '-' . $diploma->getRNCP() . '.pdf"',
                ]
            );
        }

        return $this->render('program/showProgram.html.twig', $params);
    }


    #[Route('/program/{program}/diploma/{diploma}', name: 'app_program_diploma_show')]
    public function showByDiploma(Program $program,
                                  Diploma $diploma,
                                  ModuleCompetenceAssignmentRepository $moduleCompetenceAssignmentRepository,
                                  Request $request,
                                  Environment $twig,
                                  AssignmentRepository $assignmentRepository  ): Response
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

        $moduleByPart = $assignmentRepository->getByPart($program);

        $assignments = $moduleCompetenceAssignmentRepository->findBy([
            'program' => $program,
            'diploma' => $diploma,
        ]);

        foreach ($assignments as $assignment) {
            $competence = $assignment->getCompetence();

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

                $module = $assignment->getModule();

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
            'moduleByPart' => $moduleByPart,
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
        foreach ($program->getAssignments() as $assignment) {
            $newAssignment = new Assignment();
            $newAssignment->setProgram($duplicateProgram);
            $newAssignment->setModule($assignment->getModule());
            $newAssignment->setPart($assignment->getPart());
            $em->persist($newAssignment);
        }

        // Dupliquer les affectations aux compétences
        foreach ($program->getModuleCompetenceAssignments() as $mca) {
            $newMca = new ModuleCompetenceAssignment();
            $newMca->setProgram($duplicateProgram);
            $newMca->setModule($mca->getModule());
            $newMca->setCompetence($mca->getCompetence());
            $em->persist($newMca);
        }

        $em->flush();

        $this->addFlash('success', 'Programme dupliqué avec succès.');
        return $this->redirectToRoute('app_program');
    }


    #[Route('/program/assign/{id}', name: 'app_program_assign', methods: ['GET'])]
    public function assign(Program $program, ModuleRepository $moduleRepository, AssignmentRepository $assignmentRepository): Response
    {
        $user = $this->getUser();

        if($program->getOwner() !== $user
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas éditer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $modules = $moduleRepository->getModulesWithSharedAccess($user);// Tri alphabétique côté PHP
        usort($modules, fn($a, $b) => strcasecmp($a->getTitle(), $b->getTitle()));

        $assignments = $assignmentRepository->findBy(['program' => $program]);

        $mapped = [];
        foreach ($assignments as $a) {
            $mapped[] = [
                'module' => $a->getModule()->getId(),
                'part' => $a->getPart(), // ou autre champ utilisé
            ];
        }

        return $this->render('program/assign.html.twig', [
            'program' => $program,
            'modules' => $modules,
            'assignments' => $mapped
        ]);
    }

    #[Route('/program/assign/{id}', name: 'app_program_assign_save', methods: ['POST'])]
    public function assignSave(
        Request $request,
        Program $program,
        EntityManagerInterface $em,
        AssignmentRepository $assignmentRepository,
        ModuleCompetenceAssignmentRepository $moduleCompetenceAssignmentRepository,
        ModuleRepository $moduleRepository
    ) {
        $user = $this->getUser();

        // Sécurité
        if ($program->getOwner() !== $user
            && (!in_array("ROLE_ADMIN", $user->getRoles())
                || $program->getOwner()->getSchool() !== $user->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas éditer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        // Récupérer tous les modules initialement affectés
        $existing = $assignmentRepository->findBy(['program' => $program]);
        $modulesToCleanup = [];

        foreach ($existing as $item) {
            $modulesToCleanup[] = $item->getModule(); // On garde la trace
            $em->remove($item);
        }

        // Récupération des affectations envoyées depuis le formulaire
        $assignmentsData = $request->request->get('assignments');
        $modulesKept = [];

        if ($assignmentsData) {
            $assignments = json_decode($assignmentsData, true);

            if (is_array($assignments)) {
                foreach ($assignments as $entry) {
                    if (!isset($entry['module']) || !isset($entry['part'])) continue;

                    $module = $moduleRepository->find($entry['module']);
                    if (!$module) continue;

                    $assignment = new Assignment();
                    $assignment->setProgram($program);
                    $assignment->setModule($module);
                    $assignment->setPart((int) $entry['part']);
                    $em->persist($assignment);

                    $modulesKept[] = $module;
                }
            }
        }

        // Supprimer les affectations aux compétences pour les modules non conservés
        foreach ($modulesToCleanup as $oldModule) {
            if (!in_array($oldModule, $modulesKept, true)) {
                $affectations = $moduleCompetenceAssignmentRepository->findBy([
                    'program' => $program,
                    'module' => $oldModule
                ]);

                foreach ($affectations as $a) {
                    $em->remove($a);
                }
            }
        }

        $em->flush();

        $this->addFlash('success', 'Affectations enregistrées avec succès.');
        return $this->redirectToRoute('app_program');
    }


    #[Route('/diploma/assign/{diploma}/{program}', name: 'app_diploma_assign', methods: ['GET'])]
    public function assignDiploma(
        Program $program,
        Diploma $diploma,
        ModuleRepository $moduleRepository,
        AssignmentRepository $assignmentRepository,
        ModuleCompetenceAssignmentRepository $moduleCompetenceAssignmentRepository,
        CreditRepository $creditRepository
    ): Response {
        $user = $this->getUser();
        $school = $user->getSchool();
        $solde = $creditRepository->getCreditsUsedThisMonth($school);
        $subscription = $school->getLastInvoiceValid()?$school->getLastInvoiceValid()->getSubscription(): null;

        if (
            $program->getOwner() !== $user
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas éditer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $competences = $diploma->getCompetences();
        $assignments = $assignmentRepository->findBy(['program' => $program]);

        // Regrouper les modules affectés par compétence
        $modulePerCompetence = [];
        $assignedModuleIds = [];

        foreach ($moduleCompetenceAssignmentRepository->findBy([
            'program' => $program,
            'diploma' => $diploma,
        ]) as $affectation) {
            $cid = $affectation->getCompetence()->getId();
            if (!isset($modulePerCompetence[$cid])) {
                $modulePerCompetence[$cid] = [];
            }

            $modulePerCompetence[$cid][] = $affectation->getModule();
            $assignedModuleIds[] = $affectation->getModule()->getId();
        }

        // Modules non encore affectés
        $unassignedModules = array_filter($assignments, function ($assignment) use ($assignedModuleIds) {
            return !in_array($assignment->getModule()->getId(), $assignedModuleIds);
        });

        return $this->render('program/assignDiploma.html.twig', [
            'program' => $program,
            'diploma' => $diploma,
            'credit' => $solde ?? null,
            'competences' => $competences,
            'assignments' => $assignments,
            'modulesByCompetence' => $modulePerCompetence,
            'unassignedModules' => $unassignedModules,
            'user' => $user,
            'subscription' => $subscription,
        ]);
    }


    #[Route('/diploma/assign/{diploma}/{program}', name: 'app_diploma_assign_save', methods: ['POST'])]
    public function saveAssignDiploma(
        Diploma $diploma,
        Program $program,
        Request $request,
        EntityManagerInterface $em,
        CompetenceRepository $competenceRepo,
        ModuleRepository $moduleRepo,
        ModuleCompetenceAssignmentRepository $affectationRepo
    ): Response {

        $user = $this->getUser();
        if($program->getOwner() !== $user
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $program->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas éditer ce programme.');
            return $this->redirectToRoute('app_program');
        }

        $data = json_decode($request->request->get('assignments'), true);

        // Supprimer les affectations existantes pour ce programme/diplôme
        $existing = $affectationRepo->findBy(['program' => $program, 'diploma' => $diploma]);
        foreach ($existing as $aff) {
            $em->remove($aff);
        }

        // Recréer les affectations
        foreach ($data as $item) {
            $module = $moduleRepo->find($item['module']);
            $competence = $competenceRepo->find($item['competence']);

            if ($module && $competence) {
                $affectation = new ModuleCompetenceAssignment();
                $affectation->setProgram($program);
                $affectation->setCompetence($competence);
                $affectation->setDiploma($diploma);
                $affectation->setModule($module);
                $em->persist($affectation);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Affectations enregistrées avec succès.');
        return $this->redirectToRoute('app_program');
    }

    #[Route('/program/assign/auto/{diploma}/{program}', name: 'app_program_assign_competence_auto', methods: ['POST'])]
    function assignAuto(Diploma $diploma, Program $program, Request $request,
                        ModuleCompetenceAssignmentRepository $affectationRepo,
                        EntityManagerInterface $em,ChatGptClient $chatGptClient,
                        SettingRepository $settingRepository,
                        CreditRepository $creditRepository): Response
    {
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('generate_diploma', $submittedToken)) {
            throw $this->createAccessDeniedException('CSRF token invalid');
        }
        $school = $this->getUser()->getSchool();
        $solde = $creditRepository->getCreditsUsedThisMonth($school);

        if ($solde>=200){
            $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour générer des affectations automatiques.');
            return $this->redirectToRoute('app_diploma_assign',
                [
                    'diploma' => $diploma->getId(),
                    'program' => $program->getId()
                ]);
        }


        $chatGptClient->setApiKey($settingRepository->findOneBy(['name' => 'chatGPT'])->getValue());
        $result = $chatGptClient->generateAssignations($diploma,$program);

        $credit = new Credit();
        $credit->setSchool($school);
        $credit->setQuery('module_assign');
        $credit->setCreatedAt(new \DateTimeImmutable());
        $em->persist($credit);
        $em->flush();

        $assignments = json_decode($result, true) ?: [];

        if (empty($assignments)) {
            $this->addFlash('error', 'Aucune affectation n\'a pu être réalisée .');
        }else{

            $existing = $affectationRepo->findBy(['program' => $program, 'diploma' => $diploma]);
            foreach ($existing as $aff) {
                $em->remove($aff);
            }
            foreach ($assignments as $competenceId => $modules) {
                $competence = $em->getRepository(Competence::class)->find($competenceId);
                if (!$competence) {
                    continue; // Ignore si la compétence n'existe pas
                }

                foreach ($modules as $moduleId) {
                    $module = $em->getRepository(Module::class)->find($moduleId);
                    if (!$module) {
                        continue; // Ignore si le module n'existe pas
                    }
                    $assignment = new ModuleCompetenceAssignment();
                    $assignment->setProgram($program);
                    $assignment->setCompetence($competence);
                    $assignment->setModule($module);
                    $assignment->setDiploma($diploma);
                    $em->persist($assignment);
                }
            }
            $em->flush();
        }



        return $this->redirectToRoute('app_diploma_assign',
            [
                'diploma' => $diploma->getId(),
                'program' => $program->getId()
            ]);
    }
}
