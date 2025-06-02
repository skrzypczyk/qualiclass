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
        $user = $this->getUser();
        foreach ($user->getPrograms() as $program_id=>$program) {
            foreach ($program->getDiplomas() as $diploma) {
                $data[$program_id]["title"] = $program->getTitle();
                $data[$program_id]["id"] = $program->getId();
                $data[$program_id]["diplomas"][$diploma->getId()]["id"] = $diploma->getId();
                $data[$program_id]["diplomas"][$diploma->getId()]["title"] = $diploma->getTitle();
                $data[$program_id]["diplomas"][$diploma->getId()]["RNCP"] = $diploma->getRNCP();
                $data[$program_id]["diplomas"][$diploma->getId()]["counter"] = 0;
                $data[$program_id]["diplomas"][$diploma->getId()]["duration"] = 0;
                $data[$program_id]["diplomas"][$diploma->getId()]["credit"] = 0;
            }
            foreach ($program->getAffectations() as $affectation) {
                $data[$program_id]["diplomas"][$affectation->getCompetence()->getDiploma()->getId()]["counter"]++;
                $data[$program_id]["diplomas"][$affectation->getCompetence()->getDiploma()->getId()]["duration"]+=$affectation->getModule()->getDuration();
                $data[$program_id]["diplomas"][$affectation->getCompetence()->getDiploma()->getId()]["credit"]+=$affectation->getModule()->getCredit();
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'data' => $data,
        ]);
    }

}
