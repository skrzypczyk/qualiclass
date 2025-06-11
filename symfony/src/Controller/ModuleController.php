<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Entity\Module;
use App\Entity\User;
use App\Form\CreateModuleType;
use App\Form\CreateModuleWithChatgptType;
use App\Repository\CategoryRepository;
use App\Repository\CreditRepository;
use App\Repository\ModuleRepository;
use App\Repository\SettingRepository;
use App\Service\ChatGptClient;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;


#[Route('/backoffice')]
final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(Request $request, ModuleRepository $moduleRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {

        $user = $this->getUser();
        $school = $user->getSchool();
        $subscription = $school->getLastInvoiceValid()?$school->getLastInvoiceValid()->getSubscription(): null;

        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');
        $sort = $request->query->get('sort', 'm.title');
        $direction = $request->query->get('direction', 'asc');
        $limit = $request->query->getInt('limit', 10); // 10 par défaut
        $archived = $request->query->get('archived', false);

        $queryBuilder = $moduleRepository->createQueryBuilder('m')
            ->leftJoin('m.owner', 'u');

        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $queryBuilder->andWhere('u.school = :school')
                ->setParameter('school', $school);
        }else{
            $queryBuilder->andWhere('u = :user')
                ->setParameter('user', $user);
        }

        if ($archived == '0') {
            $queryBuilder->andWhere('m.isArchived = :archived or m.isArchived IS NULL')
                ->setParameter('archived', false);
        }

        if ($search) {
            $queryBuilder->andWhere('LOWER(m.title) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        if ($categoryId) {
            $queryBuilder
                ->leftJoin('m.categories', 'cat')
                ->andWhere('cat.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        $queryBuilder->orderBy($sort, $direction);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $limit
        );

        $categories = $categoryRepository->findByOwnerOrSelf($school);

        return $this->render('module/index.html.twig', [
            'modules' => $pagination,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'sort' => $sort,
            'limit' => $limit,
            'direction' => $direction,
            'archived' => $archived,
            'school' => $school,
            'subscription' => $subscription,
            'user' => $user,
        ]);
    }

    #[Route('/module/create/WithAI', name: 'app_module_create_chatgpt')]
    public function createWithChatGpt(Request $request, CreditRepository $creditRepository, ChatGptClient $chatGptClient, EntityManagerInterface $em, SettingRepository $settingRepository ): Response
    {
        $syllabus = null;
        $form = $this->createForm(CreateModuleWithChatgptType::class);
        $form->handleRequest($request);
        $school = $this->getUser()->getSchool();

        $solde = $creditRepository->getCreditsUsedThisMonth($school);
        if ($form->isSubmitted() && $form->isValid() && $solde < 200) {
            $data = $form->getData();
            $title = $data['title'];
            $nbSessions = $data['nbSessions'] ?? 5;
            $duration = $data['duration'];
            $level = $data['level'] ?? 'débutant';

            $chatGptClient->setApiKey($settingRepository->findOneBy(['name' => 'chatGPT'])->getValue());
            $syllabus = $chatGptClient->generateStructuredSyllabus($title, $duration, $level, $nbSessions);
            $credit = new Credit();
            $credit->setSchool($school);
            $credit->setQuery('module_creation');
            $credit->setCreatedAt(new \DateTimeImmutable());
            $em->persist($credit);
            $em->flush();

            $syllabus['title'] = $title;
            $syllabus['duration'] = $duration;
            $syllabusJson = json_encode($syllabus, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }


        return $this->render('module/createWithChatGPT.html.twig', [
            'form' => $form->createView(),
            'syllabus' => $syllabus,
            'credit' => $solde ?? null,
            'syllabusJson' => isset($syllabusJson) ? $syllabusJson : null,
        ]);
    }
    #[Route('/module/insert/WithAI', name: 'app_module_store', methods: ['POST'])]
    public function insertWithChatGpt(Request $request, EntityManagerInterface $em): Response
    {
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('validate_module', $submittedToken)) {
            throw $this->createAccessDeniedException('CSRF token invalid');
        }

        $json = $request->request->get('module_data');
        $data = json_decode($json, true);
        $module = new Module();
        $module->setTitle($data['title']);
        $module->setDuration($data['duration']);
        $module->setGoal($data['description_and_objectives'] ?? 'Aucun objectif défini');
        $module->setSyllabus($data['sessions'] ?? 'Aucun plan de séance défini');
        $module->setComment($data['activities_and_resources'] ?? 'Aucun commentaire défini');
        $module->setOwner($this->getUser());
        $module->setIsShared( false);
        $em->persist($module);
        $em->flush();
        $this->addFlash('success', 'Module créé avec succès !');
        return $this->redirectToRoute('app_module');
    }

    #[Route('/module/create', name: 'app_module_create')]
    #[Route('/module/edit/{id}', name: 'app_module_edit')]
    public function createEdit(Request $request, EntityManagerInterface $em, Module $module = null, ModuleRepository $moduleRepository): Response
    {
        if ($module && $module->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $module->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ){
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce module.');
            return $this->redirectToRoute('app_module');
        }


        $create = false;
        if(!$module){
            $module = new Module();
            $module->setOwner($this->getUser());
            $create = true;
        }

        $form = $this->createForm(CreateModuleType::class, $module,[
            'user' => $this->getUser(),
        ]);

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $form->remove('owner');
            $form->remove('isShared');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $module = $form->getData();
            $em->persist($module);
            $em->flush();
            $this->addFlash('success', 'Module enregistré avec succès !');
            return $this->redirectToRoute('app_module');
        }

        return $this->render('module/createEdit.html.twig', [
            'create' => $create,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/module/delete/{id}', name: 'app_module_delete')]
    public function delete(Module $module, EntityManagerInterface $em): Response
    {
        if($module->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $module->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce module.');
            return $this->redirectToRoute('app_module');
        }
        if ($module->getAssignments()->count() > 0) {
            $this->addFlash('error', 'Ce module ne peut pas être supprimé car il est utilisé dans le programme : '.
                $module->getAssignments()->first()->getProgram()->getTitle() .
                '. Veuillez le retirer du programme avant de le supprimer.');
            return $this->redirectToRoute('app_module');
        }

        $em->remove($module);
        $em->flush();

        $this->addFlash('success', 'Module supprimé avec succès.');
        return $this->redirectToRoute('app_module');
    }


    #[Route('/module/duplicate/{id}', name: 'app_module_duplicate')]
    public function duplicate(Module $module, EntityManagerInterface $em): Response
    {
        if($module->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $module->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas dupliquer ce module.');
            return $this->redirectToRoute('app_module');
        }
        $duplicateModule = clone $module;
        $duplicateModule->setTitle($module->getTitle() . ' (copie)');
        $duplicateModule->setOwner($this->getUser());
        $em->persist($duplicateModule);
        $em->flush();

        $this->addFlash('success', 'Module dupliqué avec succès.');
        return $this->redirectToRoute('app_module');
    }


    #[Route('/module/archive/{id}', name: 'app_module_archive')]
    public function archive(Module $module, EntityManagerInterface $em): Response
    {
        if($module->getOwner() !== $this->getUser()
            && (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())
                || $module->getOwner()->getSchool() !== $this->getUser()->getSchool())
        ) {
            $this->addFlash('error', 'Vous ne pouvez pas archiver ce module.');
            return $this->redirectToRoute('app_module');
        }

        $module->setIsArchived($module->isArchived() ? false : true);
        $em->persist($module);
        $em->flush();

        $this->addFlash('success', 'Module archivé avec succès.');
        return $this->redirectToRoute('app_module');
    }
}
