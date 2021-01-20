<?php

namespace App\Controller;

use App\Entity\Subreddit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;

class SubredditController extends AbstractController
{
    /**
     * return a subreddit's homepage
     *
     * @param EntityManagerInterface $em
     * @param string $title
     * @return Response
     */
    public function index(EntityManagerInterface $em, $title): Response
    {
        $subreddit = $em->getRepository(Subreddit::class)->findOneByTitle($title);
        if($subreddit==null){
            return $this->redirectToRoute('home');
        }

        $posts = $em->getRepository(Post::class)->findAllBySubreddit($subreddit);
        return $this->render('subreddit/index.html.twig', [
            'subreddit' => $subreddit,
            'posts' => $posts,
        ]);
    }
}
