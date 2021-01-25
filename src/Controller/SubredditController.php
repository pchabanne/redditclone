<?php

namespace App\Controller;

use App\Entity\Subreddit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\SubredditRepository;
use Symfony\Component\HttpFoundation\Request;

class SubredditController extends AbstractController
{
    /**
     * return a subreddit's homepage
     * @Route("/r/{title}", name="subreddit")
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

    /**
     * @Route("r/{title}/submit", name="createPostSub")
     *
     * @return void
     */
    public function createPostFromSubreddit(Request $request, EntityManagerInterface $em, SubredditRepository $subredditRepository, Subreddit $subreddit){
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $post = new Post();
        $post->setSubreddit($subreddit);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $data = $form->getData();

        if($form->isSubmitted() && $form->isValid()){
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush($post);
            return $this->redirectToRoute('home');
        }

        return $this->render('post/createPost.html.twig', ['form'=>$form->createView(), 'post'=>$post]);
    }

    /**
     * @Route("submit", name="createPost")
     *
     * @return void
     */
    public function createPost(Request $request, EntityManagerInterface $em){
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $data = $form->getData();

        if($form->isSubmitted() && $form->isValid()){
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush($post);
            return $this->redirectToRoute('home');
        }

        return $this->render('post/createPost.html.twig', ['form'=>$form->createView(), 'post'=>$post]);
    }
}
