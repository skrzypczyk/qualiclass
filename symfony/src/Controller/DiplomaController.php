<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\Diploma;
use App\Entity\Module;
use App\Form\CreateDiplomaType;
use App\Form\ImportDiplomaType;
use App\Repository\DiplomaRepository;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice')]
final class DiplomaController extends AbstractController
{
    #[Route('/diploma', name: 'app_diploma')]
    public function index(): Response
    {
        $diplomas = $this->getUser()->getDiplomas();
        return $this->render('diploma/index.html.twig', [
            'controller_name' => 'DiplomaController',
            'diplomas' => $diplomas,
        ]);
    }

    #[Route('/diploma/import', name: 'app_diploma_import')]
    public function import(Request $request): Response
    {
        $form = $this->createForm(ImportDiplomaType::class);
        $form->handleRequest($request);

        $diplomaData = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rncp = $data['RNCP'];

            if (!filter_var($rncp, FILTER_VALIDATE_URL)) {
                $rncp = 'https://www.francecompetences.fr/recherche/rncp/' . trim($rncp, '/') . '/';
            }

            $httpClient = HttpClient::create();
            try {
                $response = $httpClient->request('GET', $rncp);
                $html = $response->getContent();
                $crawler = new Crawler($html);

                $diplomaData = [];

                // Titre de la certification
                $diplomaData['title'] = trim($crawler->filter('h2.title--page--generic')->text(''));

                // Code de la fiche RNCP
                $diplomaData['code'] = trim($crawler->filter('.tag--fcpt-certification__status')->first()->text(''));

                $diplomaData['summary'] = $crawler
                    ->filterXPath('//div[@class="accordion-content--fcpt-certification--summary"]/div[contains(@class, "text--fcpt-certification")]')
                    ->each(function (Crawler $section) {
                        $title = trim($section->filter('.text--fcpt-certification__title')->text(''));

                        // On récupère tout le contenu HTML après le titre (en gardant les <p>, <ul>, <li>, <br>, etc.)
                        $htmlParts = $section->children()
                            ->reduce(function (Crawler $node) {
                                return !str_contains($node->attr('class') ?? '', 'text--fcpt-certification__title');
                            })
                            ->each(fn(Crawler $node) => $node->outerHtml());

                        return [
                            'title' => $title,
                            'text' => implode("\n\n", $htmlParts),
                        ];
                    });

                // L'essentiel
                $diplomaData['essential'] = $crawler->filter('.list--fcpt-certification--essential--desktop__line')->each(function (Crawler $line) {
                    // Récupère le HTML du titre pour traiter les <br>
                    $rawTitle = $line->filter('p.list--fcpt-certification--essential--desktop__line__title')->html('');
                    $title = trim(strip_tags(preg_replace('/<br\s*\/?>/i', ' ', $rawTitle)));

                    // Récupère le texte associé
                    $rawHtml = $line->filter('.list--fcpt-certification--essential--desktop__line__text')->html('');
                    $cleanText = preg_replace('/<br\s*\/?>/i', ' ', $rawHtml);
                    $text = trim(strip_tags($cleanText), '<p><strong><ul><li><br>');
                    return compact('title', 'text');
                });

                // Blocs de compétences
                $diplomaData['competences'] = $crawler->filter('#anchor3 ~ .accordion--fcpt-certification__content .accordion-content--fcpt-certification--skills')->each(function (Crawler $section) {
                    $results = [];
                    $titles = $section->filter('.text--fcpt-certification__title');
                    $tables = $section->filter('.table--fcpt-certification');

                    foreach ($titles as $i => $titleNode) {
                        $titleText = trim($titles->eq($i)->text());
                        $rows = $tables->eq($i)->filter('tbody tr')->first()->filter('td');

                        $competences = $rows->eq(0)->html('');
                        $evaluation = $rows->eq(1)->html('');
                        if($competences !== '' || $evaluation !== '') {
                            $results[] = [
                                'title' => $titleText,
                                'competences' => strip_tags($competences, '<li>'),
                                'evaluation' => strip_tags($evaluation, '<p><strong>'),
                            ];
                        }
                    }

                    return $results;
                })[0] ?? [];
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Impossible de récupérer les données depuis France Compétences.');
            }
        }

        return $this->render('diploma/import.html.twig', [
            'form' => $form->createView(),
            'diploma' => $diplomaData,
        ]);
    }

    #[Route('/diploma/validate', name: 'app_diploma_store', methods: ['POST'])]
    public function validate(Request $request, EntityManagerInterface $em): Response
    {

        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('validate_diploma', $submittedToken)) {
            throw $this->createAccessDeniedException('CSRF token invalid');
        }

        $json = $request->request->get('diploma_data');
        $data = json_decode($json, true);

        if (!$data) {
            throw new \RuntimeException('Invalid data');
        }
        $diploma = new Diploma();
        $diploma->setTitle($data['title'] ?? 'Diplôme sans titre');
        $diploma->setRNCP($data['code'] ?? 'Code non spécifié');
        $content = "";
        foreach ($data['essential'] as $essential) {
            $content .= "<b>".$essential['title'] ."</b> : ".trim($essential['text']) ."<br>";
        }
        $content .= "<br>";
        foreach ($data['summary'] as $summary) {
            $content .= "<h1><b>".trim($summary['title']) ."</b></h1> ".trim($summary['text']) ."<br>";
        }
        $diploma->setContent($content);
        $diploma->setOwner($this->getUser());
        foreach ($data['competences'] as $competenceData) {
            $competence = new Competence();
            $titleExploded = explode(' - ', $competenceData['title']);
            $competence->setTitle($titleExploded[1]?? 'Compétence sans titre');
            $competence->setContent(trim($competenceData['competences'])."<br>".trim($competenceData['evaluation']));
            $competence->setRNCP($titleExploded[0]?? 'RNCP non spécifié');
            $diploma->addCompetence($competence);
        }

        // ... ici, insérer l'objet $data dans la BDD via un service / entité custom
        $em->persist($diploma);
        $em->flush();

        $this->addFlash('success', 'Diplôme inséré avec succès !');
        return $this->redirectToRoute('app_diploma');
    }

    #[Route('/diploma/create', name: 'app_diploma_create')]
    #[Route('/diploma/edit/{id}', name: 'app_diploma_edit')]
    public function createEdit(Request $request, EntityManagerInterface $em, Diploma $diploma = null, DiplomaRepository $diplomaRepository): Response
    {
        $create = false;
        if ($diploma === null) {
            $create = true;
            $diploma = new Diploma();
            $diploma->setOwner($this->getUser());
        } else {
            if ($diploma->getOwner() !== $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier ce diplôme.');
                return $this->redirectToRoute('app_diploma');
            }
        }

        $form = $this->createForm(CreateDiplomaType::class, $diploma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($diploma);
            $em->flush();

            $this->addFlash('success', 'Diplôme enregistré avec succès.');
            return $this->redirectToRoute('app_diploma');
        }

        return $this->render('diploma/createEdit.html.twig', [
            'form' => $form->createView(),
            'diploma' => $diploma,
            'create' => $create
        ]);
    }

    #[Route('/delete/{id}', name: 'app_diploma_delete')]
    public function delete(Diploma $diploma, EntityManagerInterface $em): Response
    {
        if($diploma->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce diplôme.');
            return $this->redirectToRoute('app_diploma');
        }

        $em->remove($diploma);
        $em->flush();

        $this->addFlash('success', 'diplôme supprimé avec succès.');
        return $this->redirectToRoute('app_diploma');
    }


    #[Route('/duplicate/{id}', name: 'app_diploma_duplicate')]
    public function duplicate(Diploma $diploma, EntityManagerInterface $em): Response
    {
        if($diploma->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas dupliquer ce diplôme.');
            return $this->redirectToRoute('app_diploma');
        }
        $duplicateDiploma = clone $diploma;
        $duplicateDiploma->setTitle($diploma->getTitle() . ' (copie)');
        $duplicateDiploma->setOwner($this->getUser());
        $em->persist($duplicateDiploma);
        $em->flush();

        $this->addFlash('success', 'Diplôme dupliqué avec succès.');
        return $this->redirectToRoute('app_diploma');
    }
}
