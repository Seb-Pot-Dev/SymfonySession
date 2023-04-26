<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Security $security, ManagerRegistry $doctrine): Response
    {
        $user=$security->getUser();
        if($user){
            $users = $doctrine->getRepository(User::class)->findAll();
            return $this->render('user/index.html.twig', [
                'controller_name' => 'UserController',
                'users'=>$users,
            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
    #[Route('/user/remove/{id}', name: 'remove_user')]
    public function remove(Security $security, EntityManagerInterface $entityManager, user $user = null): Response
    {
        $current_user=$security->getUser();
        if($security->isGranted("ROLE_ADMIN") && $current_user){
            if($user){
                $entityManager->remove($user);
                $entityManager->flush();
            
                return $this->redirectToRoute('app_register');
            }
            else{
                return $this->redirectToRoute('app_register');
            }
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
}

