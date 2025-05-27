<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\User;
use App\Form\CreateModuleType;
use App\Repository\CategoryRepository;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice')]
final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(Request $request, ModuleRepository $moduleRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {

        $user = $this->getUser();

        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');
        $sort = $request->query->get('sort', 'm.title');
        $direction = $request->query->get('direction', 'asc');
        $limit = $request->query->getInt('limit', 10); // 10 par défaut
        $archived = $request->query->get('archived', false);

        $queryBuilder = $moduleRepository->createQueryBuilder('m')
            ->leftJoin('m.owner', 'u') // relation entre Module et User
            ->leftJoin('m.affectations', 'a') // relation entre Module et ModuleCompetenceAffectation
            ->leftJoin('a.program', 'p') // relation entre ModuleCompetenceAffectation et Competence
            ->where('u = :user')
            ->orWhere('u IN (:ledUsers)')
            ->setParameter('user', $user)
            ->setParameter('ledUsers', $user->getUsers());

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

        $categories = $categoryRepository->findByOwnerOrSelf($user);

        return $this->render('module/index.html.twig', [
            'modules' => $pagination,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'sort' => $sort,
            'limit' => $limit,
            'direction' => $direction,
            'archived' => $archived,
        ]);
    }

    #[Route('/module/create', name: 'app_module_create')]
    #[Route('/module/edit/{id}', name: 'app_module_edit')]
    public function createEdit(Request $request, EntityManagerInterface $em, Module $module = null, ModuleRepository $moduleRepository): Response
    {
        if ($module && !$moduleRepository->userCanAccessModule($this->getUser(), $module)) {
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
        if($module->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce module.');
            return $this->redirectToRoute('app_module');
        }
        if ($module->getAffectations()->count() > 0) {
            $this->addFlash('error', 'Ce module ne peut pas être supprimé car il est utilisé dans un programme.');
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
        if($module->getOwner() !== $this->getUser()) {
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
        if($module->getOwner() !== $this->getUser()) {
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
