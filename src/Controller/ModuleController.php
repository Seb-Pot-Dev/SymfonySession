<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ManagerRegistry $doctrine, Module $module = null, Request $request): Response
    {
         //récupérer les module de la base de donnees
         $modules = $doctrine->getRepository(Module::class)->findAll();

        //FORMULAIRE -------------
            //Construire un formulaire qui se repose sur le $builder présent dans le moduleFormType
            $form = $this->createForm(ModuleFormType::class, $module);
            //Quand il y a une action dans le formulaire, analyse ce que récupère la requete 
            $form->handleRequest($request);

            //Si le formulaire est soumis et passe les filtres de sécurité
            if($form->isSubmitted() && $form->isValid()){

                //récupère les données du formulaire saisies et les injectent 'hydrater' via les setter dans l'objet entreprise.
                $module = $form->getData();
                //On récupère le manager de doctrine pour accéder aux méthodes suivantes
                $entityManager = $doctrine->getManager();
                //On prépare notre objet
                $entityManager->persist($module);
                //On execute notre objet pour insérer les données en BDD.
                $entityManager->flush();

                return $this->redirectToRoute('app_module');
            }
        //FIN FOMULAIRE ---------
        return $this->render('module/index.html.twig', [
            'controller_name' => 'ModuleController',
            'modules' => $modules,
            //Pour créer le formulaire dans la view
            'formAddModule'=>$form->createView()

        ]);
    }
    #[Route('/module/{id}', name: 'show_module')]
    public function show(Module $module): Response
    //On appel l'objet module dont l'id est passé en parametre par la route
    {

        //renvoie la vue et associe des données
        return $this->render('module/show.html.twig', [
            'controller_name' => 'ModuleController',
            'module' => $module
        ]);
    }

    #[Route('/module/remove/{id}', name: 'remove_module')]
    public function remove(EntityManagerInterface $entityManager, Module $module = null): Response
    {
        if($module){
            $entityManager->remove($module);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_module');
        }
        else{
            return $this->redirectToRoute('app_module');
        }
    }
}
