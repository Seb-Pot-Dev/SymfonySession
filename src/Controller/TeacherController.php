<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{
    #[Route('/teacher', name: 'app_teacher')]
    public function index(ManagerRegistry $doctrine, Teacher $teacher = null, Request $request): Response
    {
        //récupérer les profs de la base de donnees
        $teachers = $doctrine->getRepository(Teacher::class)->findAll();

        //FORMULAIRE -------------
            //Construire un formulaire qui se repose sur le $builder présent dans le courseFormType
            $form = $this->createForm(TeacherFormType::class, $teacher);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);

            //Si le formulaire est soumis et passe les filtres de sécurité
            if($form->isSubmitted() && $form->isValid()){

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $teacher = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($teacher);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_teacher');
            }
        //FIN FOMULAIRE ---------

        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            'teachers' => $teachers,
            //Pour créer le formulaire dans la view
            'formAddTeacher'=>$form->createView()
        ]);
    }
}
