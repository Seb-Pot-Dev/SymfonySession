<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(Security $security, ManagerRegistry $doctrine, Student $student = null, Request $request): Response
    {
        $user=$security->getUser();
        if($user){
            $students = $doctrine->getRepository(Student::class)->findAll();

            //FORMULAIRE -------------
            $student = new Student();
            //Construire un formulaire qui se repose sur le $builder présent dans le studentFormType
            $form = $this->createForm(StudentFormType::class, $student);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);



            //Si le formulaire est soumis et passe les filtres de sécurité
            if ($form->isSubmitted() && $form->isValid()) {

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $student = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($student);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_student');
            }
            //FIN FOMULAIRE ---------

            return $this->render('student/index.html.twig', [
                'controller_name' => 'StudentController',
                'students' => $students,
                'formAddStudent' => $form->createView()

            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
    #[Route('/student/remove/{id}', name: 'remove_student')]
    public function remove(Security $security, EntityManagerInterface $entityManager, Student $student = null): Response
    {
        $user=$security->getUser();
        if($user){
            if($student){
                $entityManager->remove($student);
                $entityManager->flush();
            
                return $this->redirectToRoute('app_student');
            }
            else{
                return $this->redirectToRoute('app_student');
            }
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
}
