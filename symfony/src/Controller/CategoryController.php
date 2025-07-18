<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CreateCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice')]
final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    #[Route('/category/{id}', name: 'app_category_edit')]
    public function index(EntityManagerInterface $em, Request $request, Category $category = null): Response
    {
        $create = false;
        $user = $this->getUser();
        $school = $user->getSchool();
        if (is_null($category)){
            $category = new Category();
            $create = true;
        }else if ($category->getSchool() !== $school)
        {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette catégorie !');
            return $this->redirectToRoute('app_category');
        }
        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setSchool($school);
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', ($create ? 'Catégorie créée avec succès !' : 'Catégorie modifiée avec succès !'));
            return $this->redirectToRoute('app_category');
        }
        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'categories' => $school->getCategories(),
            'create' => $create,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function delete(EntityManagerInterface $em, Request $request, Category $category = null): Response
    {
        $user = $this->getUser();
        if ($category->getSchool() !== $user->getSchool() || is_null($category))
        {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette catégorie !');
            return $this->redirectToRoute('app_category');
        }

        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Catégorie supprimée avec succès !');
        return $this->redirectToRoute('app_category');
    }
}
