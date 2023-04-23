<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Student;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ManagerRegistry $doctrine, session $session = null, Request $request): Response
    {
        $sessions= $doctrine->getRepository(Session::class)->findBy([], ['name' => 'ASC']);
        
        //FORMULAIRE -------------
            //Construire un formulaire qui se repose sur le $builder présent dans le sessionFormType
            $form = $this->createForm(SessionFormType::class, $session);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);

            //Si le formulaire est soumis et passe les filtres de sécurité
            if($form->isSubmitted() && $form->isValid()){

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $session = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($session);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_session');
            }
        //FIN FOMULAIRE ---------

        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
            'sessions' => $sessions,
            'formAddSession'=>$form->createView()
        ]);
    }
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr): Response
    //On appel l'objet session dont l'id est passé en parametre par la route
    {
        //récupère l'id de la session
        $session_id=$session->getId();
        //récupère tous les étudiants NON-INSCRITS a cette session
        $NotScheduledStudents = $sr->findNotScheduledStudents($session_id);
        $NotScheduledModules = $sr->findNotScheduledModules($session_id);

        //renvoie la vue et associe des données
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'available_students' => $NotScheduledStudents,
            'available_modules'=> $NotScheduledModules

            // 'plannings' => $plannings
        ]);
    }
    
    #[Route("/session/addStudent/{idSe}/{idSt}", name: 'add_student')]
    #[ParamConverter("session", option:["mapping"=>["idSe"=>"id"]])]
    #[ParamConverter("student", option:["mapping"=>["idSt"=>"id"]])]
    public function addStudent(ManagerRegistry $doctrine, Session $session, Student $student)
    {
        $entityManager = $doctrine->getManager();

        $session->addStudent($student);

        $entityManager->persist($session);

        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }
}
