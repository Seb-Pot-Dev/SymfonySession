<?php

namespace App\Controller;

use doctrine;
use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(ManagerRegistry $doctrine, Category $category = null, Request $request): Response
    //On appel le manager de doctrine 
    {
        //récupérer les Category de la base de donnees
        $categories = $doctrine->getRepository(Category::class)->findBy([], ["name" => "ASC"]);

                //FORMULAIRE -------------
            //Construire un formulaire qui se repose sur le $builder présent dans le CategoryFormType
            $form = $this->createForm(CategoryFormType::class, $category);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);

            //Si le formulaire est soumis et passe les filtres de sécurité
            if($form->isSubmitted() && $form->isValid()){

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $category = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($category);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_category');
            }
                //FIN FOMULAIRE ---------
        //renvoie la vue et associe des données
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
            'formAddCategory'=>$form->createView()
        ]);
    }

    // #[Route('/category', name: 'add_category')]
    // //Doctrine pour intéragir avec la BDD
    // //Category pour dire quel type d'élément on ajoute en BDD
    // //Request pour ...
    // public function add(ManagerRegistry $doctrine, Category $category = null, Request $request): Response 
    // {
    //     //Construire un formulaire qui se repose sur le $builder présent dans le CategoryFormType
    //     $form = $this->createForm(CategoryFormType::class, $category);
    //     //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
    //     $form->handleRequest($request);

    //     //Si le formulaire est soumis et passe les filtres de sécurité
    //     if($form->isSubmitted() && $form->isValid()){

    //         //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
    //         $category = $form->getData();
    //         //On récupère le manager de doctrine pour accéder aux méthodes suivantes
    //         $entityManager = $doctrine->getManager();
    //         //On prépare notre objet
    //         $entityManager->persist($category);
    //         //On execute notre objet pour insérer les données en BDD.
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_category');
    //     }

    //     //redirection vers la vue add (liste des catégories)
    //     return $this->render('category/add.html.twig', [
    //         'formAddCategory'=>$form->createView()
    //     ]);
    // }

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
