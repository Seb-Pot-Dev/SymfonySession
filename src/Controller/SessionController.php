<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $sessions= $doctrine->getRepository(Session::class)->findBy([], ['name' => 'ASC']);
        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
            'sessions' => $sessions
        ]);
    }
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response
    //On appel l'objet session dont l'id est passé en parametre par la route
    {

        //renvoie la vue et associe des données
        return $this->render('session/show.html.twig', [
            'controller_name' => 'SessionController',
            'session' => $session
        ]);
    }
}
