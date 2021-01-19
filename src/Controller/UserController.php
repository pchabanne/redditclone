<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Post;


class UserController extends AbstractController
{
    /**
     * return the user's page
     * @Route("/{username}", name="showUser")
     * @param EntityManagerInterface $em
     * @param string $username
     * @return Response
     */
    public function index(EntityManagerInterface $em, $username): Response
    {
        $user = $em->getRepository(User::class)->findOneByUsername($username);
        if($user == null){
            return $this->redirectToRoute('home');
        }
        
        $posts = $em->getRepository(Post::class)->findAllByUser($user);
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
