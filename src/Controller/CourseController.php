<?php

namespace App\Controller;

use App\Entity\Course;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    #[Route('/course', name: 'app_course')]
    public function index(ManagerRegistry $doctrine): Response
    //On appel le manager de doctrine 
    {
        //récupérer les course de la base de donnees
        $courses = $doctrine->getRepository(Course::class)->findAll();

        //renvoie la vue et associe des données
        return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'courses' => $courses
        ]);
    }

    #[Route('/course/{id}', name: 'show_course')]
    public function show(Course $course): Response
    //On appel l'objet course dont l'id est passé en parametre par la route
    {

        //renvoie la vue et associe des données
        return $this->render('course/show.html.twig', [
            'controller_name' => 'courseController',
            'course' => $course
        ]);
    }
}
