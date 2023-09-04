<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\CategoryVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class CategoryController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/category', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $categories = $categoryRepository->findAll();
        $allowedCategories = [];

        foreach ($categories as $category) {
            $isAuthor = ($user === $category->getUser());
            $hasSameRole = ($user->getRoles() === $category->getUser()->getRoles());

            $isAllowed = $this->isGranted(CategoryVoter::VIEW, $category, $isAdmin, $isAuthor, $hasSameRole);

            if ($isAllowed) {
                $allowedCategories[] = $category;
            }
        }

        return $this->render('category/index.html.twig', [
            'categories' => $allowedCategories,
        ]);
    }

    #[Route('/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $category->setUser($user);

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{id}/boards', name: 'category_boards')]
    public function showBoards(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }

        $user = $this->getUser();
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isAuthor = ($user === $category->getUser());
        $hasSameRole = ($user->getRoles() === $category->getUser()->getRoles());

        $isAllowed = $this->isGranted(CategoryVoter::VIEW, $category, $isAdmin, $isAuthor, $hasSameRole);

        if (!$isAllowed) {
            throw $this->createAccessDeniedException('You do not have access to view this category.');
        }

        return $this->render('board/index.html.twig', [
            'boards' => $category->getBoards(),
        ]);
    }
}