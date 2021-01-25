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
use Symfony\Component\HttpFoundation\RequestStack;

class SubredditController extends AbstractController
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * return a subreddit's homepage
     * @Route("/r/{title}", name="subreddit")
     * @param EntityManagerInterface $em
     * @param string $title
     * @return Response
     */
    public function index($title): Response
    {
        $subreddit = $this->em->getRepository(Subreddit::class)->findOneByTitle($title);
        if($subreddit==null){
            return $this->redirectToRoute('home');
        }

        $posts = $this->em->getRepository(Post::class)->findAllBySubreddit($subreddit);
        return $this->render('subreddit/index.html.twig', [
            'subreddit' => $subreddit,
            'posts' => $posts,
        ]);
    }

    /**
     * create a post from a subreddit
     * @Route("r/{title}/submit", name="createPostSub")
     *
     * @param Subreddit $subreddit
     * @return Response
     */
    public function createPostFromSubreddit(Subreddit $subreddit) :Response{
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $post = new Post();
        $post->setSubreddit($subreddit);
        $form = $this->createForm(PostType::class, $post);
        $request = $this->requestStack->getCurrentRequest();
        $form->handleRequest($request);
        $data = $form->getData();

        if($form->isSubmitted() && $form->isValid()){
            $post->setUser($this->getUser());
            $this->em->persist($post);
            $this->em->flush($post);
            return $this->redirectToRoute('home');
        }

        return $this->render('post/createPost.html.twig', ['form'=>$form->createView(), 'post'=>$post]);
    }

    /**
     * create a post
     * @Route("submit", name="createPost")
     * @return Response
     */
    public function createPost() :Response{
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $request = $this->requestStack->getCurrentRequest();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setUser($this->getUser());
            $this->em->persist($post);
            $this->em->flush($post);
            return $this->redirectToRoute('home');
        }

        return $this->render('post/createPost.html.twig', ['form'=>$form->createView(), 'post'=>$post]);
    }
}
