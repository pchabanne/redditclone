<?php

namespace App\Controller;

use App\Entity\Subreddit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\SubredditRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SubredditController extends AbstractController
{
    private $requestStack;
    private $em;
    private $subredditRepository;
    private $postRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em, PostRepository $postRepository, SubredditRepository $subredditRepository)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->subredditRepository = $subredditRepository;
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
        $subreddit = $this->subredditRepository->findOneByTitle($title);
        if($subreddit==null){
            return $this->redirectToRoute('home');
        }

        $posts = $this->postRepository->findAllInSubredditOrderByDateAjax(0, 8, $subreddit);
        return $this->render('subreddit/index.html.twig', [
            'subreddit' => $subreddit,
            'posts' => $posts,
        ]);
    }

    /**
     * Undocumented function
     * @Route("join/{id}", name="subreddit.join")
     * @param [type] $id
     * @return void
     */
    public function joinSubreddit($id){
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }
        $subreddit = $this->subredditRepository->findOneBy(['id'=>$id]);
        $subreddits = $user->getSubreddits();
        if (!$subreddits->contains($subreddit)) {
            $user->addSubreddit($subreddit);
        }else{
            $user->removeSubreddit($subreddit);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('subreddit',['title'=>$subreddit->getTitle()]);

    }

    /**
     * @Route("/get/posts/{title}", name="ajax_subreddit_posts")
     *
     * @param [type] $title
     * @return void
     */
    public function ajaxSubreddit($title){
        $request = $this->requestStack->getCurrentRequest();
        $first = $request->query->get('first');
        $limit = $request->query->get('limit');
        $subreddit = $this->subredditRepository->findOneByTitle($title);
        $posts = $this->postRepository->findAllInSubredditOrderByDateAjax($first, $limit, $subreddit);
        $postsJson = [];
        foreach($posts as $post){
            $postJson = array();
            $postJson['title'] = $post->getTitle();
            $postJson['content'] = substr($post->getContent(), 0, 100);
            $postJson['subreddit'] = $post->getSubreddit()->getTitle();
            $postJson['user'] = $post->getUser()->getUsername();
            $postJson['date'] = $post->getCreatedAt()->format('c');
            $slug = $post->getSlug();
            $slug = $slug.'-'.$post->getId();
            $postJson['slug'] = $slug;
            $postJson['id'] = $post->getId();
            $postJson['count'] = $post->getCountLikes();
            if($this->getUser()){
                $postJson['isLiked'] = $post->isLikedByUser($this->getUser());
                $postJson['isDisliked'] = $post->isDislikedByUser($this->getUser());
            }else{
                $postJson['isLiked'] = false;
                $postJson['isDisliked'] = false;
            }

            array_push($postsJson, $postJson);
        }

        $response = new Response(json_encode($postsJson));



        $response->headers->set('Content-Type', 'application/json');

        return $response;
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
