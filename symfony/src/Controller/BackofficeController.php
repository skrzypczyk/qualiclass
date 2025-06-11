<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice')]
final class BackofficeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {

        $data = [];
        $programs = [];
        $user = $this->getUser();

        if( $user->isAdmin()){
            $users = $user->getSchool()->getUsers();
            foreach ($users as $u) {
                $programs = array_merge($programs, $u->getPrograms()->toArray());
            }
        }else{
            $programs = $user->getPrograms();
        }

        foreach ($programs as $program_id=>$program) {
            foreach ($program->getDiplomas() as $diploma) {
                $data[$program_id]["title"] = $program->getTitle();
                $data[$program_id]["id"] = $program->getId();
                $data[$program_id]["author"] = $program->getOwner()->getEmail();
                $data[$program_id]["counter"] = 0;
                $data[$program_id]["duration"] = 0;
                $data[$program_id]["credit"] = 0;
                foreach ($program->getAssignments() as $assignment) {
                    $data[$program_id]["counter"]++;
                    $data[$program_id]["duration"]+=$assignment->getModule()->getDuration();
                    $data[$program_id]["credit"]+=$assignment->getModule()->getCredit();
                }
                $data[$program_id]["diplomas"][$diploma->getId()]["id"] = $diploma->getId();
                $data[$program_id]["diplomas"][$diploma->getId()]["title"] = $diploma->getTitle();
                $data[$program_id]["diplomas"][$diploma->getId()]["RNCP"] = $diploma->getRNCP();
                $data[$program_id]["diplomas"][$diploma->getId()]["counter"] = 0;
                $data[$program_id]["diplomas"][$diploma->getId()]["duration"] = 0;
                $data[$program_id]["diplomas"][$diploma->getId()]["credit"] = 0;
            }
            foreach ($program->getModuleCompetenceAssignments() as $assignmentDiploma) {
                if(isset($data[$program_id]["diplomas"][$assignmentDiploma->getDiploma()->getId()])){
                    $data[$program_id]["diplomas"][$assignmentDiploma->getDiploma()->getId()]["counter"]++;
                    $data[$program_id]["diplomas"][$assignmentDiploma->getDiploma()->getId()]["duration"]+=$assignmentDiploma->getModule()->getDuration();
                    $data[$program_id]["diplomas"][$assignmentDiploma->getDiploma()->getId()]["credit"]+=$assignmentDiploma->getModule()->getCredit();
                }
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'data' => $data,
        ]);
    }

}
