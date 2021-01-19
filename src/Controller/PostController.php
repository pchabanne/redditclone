<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * return the post's page
     * @Route("/post/{id}", name="post")
     * @param [type] $id
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index($id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
        return $this->render('post/index.html.twig', [
            'post' => $post,
        ]);
    }
}
