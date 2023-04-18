<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(ManagerRegistry $doctrine): Response
    //On appel le manager de doctrine 
    {
        //récupérer les Category de la base de donnees
        $categories = $doctrine->getRepository(Category::class)->findBy([], ["name" => "ASC"]);

        //renvoie la vue et associe des données
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
    #[Route('/category/{id}', name: 'show_category')]
    public function show(Category $category): Response
    //On appel l'objet Category dont l'id est passé en parametre par la route
    {

        //renvoie la vue et associe des données
        return $this->render('category/show.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category
        ]);
    }
    
}
