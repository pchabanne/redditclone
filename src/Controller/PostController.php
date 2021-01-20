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
     * @Route("r/{subreddit}/{slug}-{id}", name="post.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param integer $id
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(Post $post, $slug, $subreddit, PostRepository $postRepository): Response
    {
        $postSubreddit = $post->getSubreddit()->getTitle();
        $postSlug = $post->getSlug();

        if($slug != $postSlug || $subreddit != $postSubreddit){
            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $postSlug,
                'subreddit' => $postSubreddit,
            ], 301);
        }
        
        return $this->render('post/index.html.twig', [
            'post' => $post,
        ]);
    }
}
