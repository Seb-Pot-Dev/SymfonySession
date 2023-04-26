<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users'=>$users,
        ]);
    }
    #[Route('/user/remove/{id}', name: 'remove_user')]
    public function remove(EntityManagerInterface $entityManager, user $user = null): Response
    {
        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_user');
        }
        else{
            return $this->redirectToRoute('app_user');
        }
    }
}
