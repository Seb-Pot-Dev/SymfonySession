<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    #[Route('/course', name: 'app_course')]
    public function index(ManagerRegistry $doctrine, Course $course = null, Request $request): Response
    //On appel le manager de doctrine 
    {
        //récupérer les course de la base de donnees
        $courses = $doctrine->getRepository(Course::class)->findAll();

        //FORMULAIRE -------------
            //Construire un formulaire qui se repose sur le $builder présent dans le courseFormType
            $form = $this->createForm(CourseFormType::class, $course);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);

            //Si le formulaire est soumis et passe les filtres de sécurité
            if($form->isSubmitted() && $form->isValid()){

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $course = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($course);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_course');
            }
        //FIN FOMULAIRE ---------


        //renvoie la vue et associe des données
        return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'courses' => $courses,
            //Pour créer le formulaire dans la view
            'formAddCourse'=>$form->createView()

        ]);

    }

    #[Route('/course/remove/{id}', name: 'remove_course')]
    public function remove(EntityManagerInterface $entityManager, Course $course = null): Response
    {
        if($course){
            $entityManager->remove($course);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_course');
        }
        else{
            return $this->redirectToRoute('app_course');
        }
    }

    #[Route('/course/{id}', name: 'show_course')]
    public function show(Course $course): Response
    //On appel l'objet course dont l'id est passé en parametre par la route
    {

        //renvoie la vue et associe des données
        return $this->render('course/show.html.twig', [
            'controller_name' => 'CourseController',
            'course' => $course
        ]);
    }
}
