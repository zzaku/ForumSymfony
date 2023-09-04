<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/home', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository    ): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('connexion');
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $categories = $categoryRepository->findAll(); // Vous pouvez utiliser une autre méthode pour obtenir les catégories si nécessaire

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'isAdmin' => $isAdmin,
        ]);
    }
}