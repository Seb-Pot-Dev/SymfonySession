<?php

namespace App\Controller;

use doctrine;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Student;
use App\Entity\Planning;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
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
    
    //Pour ajouter un étudiant disponible à la session
    #[Route("/session/addStudent/{idSe}/{idSt}", name: 'add_student')]
    #[ParamConverter("session", options:["mapping"=>["idSe"=>"id"]])]
    #[ParamConverter("student", options:["mapping"=>["idSt"=>"id"]])]
    public function addStudent(ManagerRegistry $doctrine, Session $session =null, Student $student=null)
    {
        if($session && $student && $session->getAvailableNbPlace()>=1){

            

            $entityManager = $doctrine->getManager();
    
            $session->addStudent($student);
    
            $entityManager->persist($session);
    
            $entityManager->flush();
    
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }
        else{
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }
    }

    // Pour supprimer un étudiant de la session et le rendre disponible
    #[Route("/session/removeSessionStudent/{idSe}/{idSt}", name: 'remove_session_student')]
    #[ParamConverter("session", options:["mapping"=>["idSe"=>"id"]])]
    #[ParamConverter("student", options:["mapping"=>["idSt"=>"id"]])]
    public function removeStudent(ManagerRegistry $doctrine, Session $session, Student $student)
    {
        $entityManager = $doctrine->getManager();

        $session->removeStudent($student);

        $entityManager->persist($session);

        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }
    
    // Pour supprimer un planning de la session et rendre le module disponible
    #[Route("/session/removePlanning/{idSe}/{idPl}", name: 'remove_planning')]
    #[ParamConverter("session", options:["mapping"=>["idSe"=>"id"]])]
    #[ParamConverter("planning", options:["mapping"=>["idPl"=>"id"]])]
    public function removePlanning(ManagerRegistry $doctrine, Session $session, Planning $planning)
    {
        $entityManager = $doctrine->getManager();

        $session->removePlanning($planning);

        $entityManager->persist($session);

        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    // Pour ajouter un module à la session et le transformer en planning
    #[Route("/session/addPlanning/{idSe}/{idMo}", name: 'add_planning')]
    #[ParamConverter("session", options:["mapping"=>["idSe"=>"id"]])]
    #[ParamConverter("module", options:["mapping"=>["idMo"=>"id"]])]
    public function addPlanning(Request $request, ManagerRegistry $doctrine, Session $session=null, Module $module=null)
    {
        if($session && $module){
        //défini un nouveau planning 
        $newPlanning= new Planning();
           
        //pour récupérer le nb de jour du form 
        $nbJours=$request->request->get('nbDay');
            if($nbJours>=1 && $nbJours<=365)
            {
            //défini le nb de jour
            $newPlanning->setNbDay($nbJours);

            //défini le module concerné
            $newPlanning->setModule($module);
            
            //défini le manager de doctrine
            $entityManager = $doctrine->getManager();
            
            //ajoute le nouveau planning à la session concerné
            $session->addPlanning($newPlanning);
            
            //dire à doctrine que j'aimerais possiblement ajouter le planning créé dans la BDD
            $entityManager->persist($newPlanning);

            //Ajoute effectivement le produit en bdd
            $entityManager->flush();
            
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
            }
            else{
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
            }
        }
        else{
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }
}

    #[Route('/session/remove/{id}', name: 'remove_session')]
    public function remove(EntityManagerInterface $entityManager, Session $session = null): Response
    {
        if($session){
            $entityManager->remove($session);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_session');
        }
        else{
            return $this->redirectToRoute('app_session');
        }
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session = null, SessionRepository $sr): Response
    //On appel l'objet session dont l'id est passé en parametre par la route
    {
        //si session existe (initalisé a null)
        if($session) {
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
        } else {
            return $this->redirectToRoute('app_session');
        }
    }
}
