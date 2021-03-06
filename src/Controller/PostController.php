<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AddComment;
use App\Service\LikeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    private $requestStack;
    private $em;
    private $postRepository;
    private $postLikeRepository;
    private $likeManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em, PostRepository $postRepository, PostLikeRepository $postLikeRepository, LikeManager $likeManager)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->requestStack = $requestStack;
        $this->postLikeRepository = $postLikeRepository;
        $this->likeManager = $likeManager;
    }
    /**
     * return the post's page
     * @Route("r/{subreddit}/{slug}-{id}", name="post.show", requirements={"slug": "[a-z0-9\-]*"})
     *
     * @param int $id
     * @param string $slug
     * @param int $subreddit
     * @return Response
     */
    public function index($id, $slug, $subreddit): Response
    {
        $post = $this->postRepository->find($id);
        if($post == null){
            return $this->redirectToRoute('home');
        }

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

    /**
     * add a comment on a post or on a comment
     * @Route("/addcomment", name="comment.add")
     *
     * @param AddComment $addComment
     * @return Response
     */
    public function addComment(AddComment $addComment): Response{
        $request = $this->requestStack->getCurrentRequest();
        $content = $request->request->get('comment');
        $response = $addComment->checkParameters($content);
        if($response!=null){
            return $this->redirect($response);
        }
        $post = $addComment->addComment($content);
        $id = $post->getId();
        $slug = $post->getSlug();
        $subreddit = $post->getSubreddit()->getTitle();

        return $this->redirectToRoute('post.show', [
            'id' => $id,
            'slug'=>$slug,
            'subreddit' => $subreddit,
        ]);
    }

    /**
     * like a post
     * @Route("post/{id}/like", name="post.like")
     * @param Post $post
     * @return Response
     */
    public function like(Post $post) :Response{
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message'=>'Unauthorized'], 403);
        }
        $contentOfRequest = json_decode($this->requestStack->getCurrentRequest()->getContent(), true);
        $token = $contentOfRequest["token"];
        $isTokenValid = $this->isCsrfTokenValid($user->getId(), $token);
        if($isTokenValid){
            $response = $this->likeManager->handleLike($post);
            return $this->json($response, 200);
        }
        return $this->json(['message'=>'token invalid'], 403);
    }

    /**
     * dislike a post
     * @Route("post/{id}/dislike", name="post.dislike")
     * 
     * @param Post $post
     * @return Response
     */
    public function dislike(Post $post) :Response{
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message'=>'Unauthorized'], 403);
        }
        $contentOfRequest = json_decode($this->requestStack->getCurrentRequest()->getContent(), true);
        $token = $contentOfRequest["token"];
        $isTokenValid = $this->isCsrfTokenValid($user->getId(), $token);
        if($isTokenValid){
            $response = $this->likeManager->handleDislike($post);
            return $this->json($response, 200);
        }
        return $this->json(['message'=>'token invalid'], 403);
    }
}
