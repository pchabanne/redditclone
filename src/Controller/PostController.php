<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AddComment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    private $em;
    private $postRepository;

    public function __construct(EntityManagerInterface $em, PostRepository $postRepository)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
    }
    /**
     * return the post's page
     * @Route("r/{subreddit}/{slug}-{id}", name="post.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param integer $id
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
     * @param Request $request
     * @param AddComment $addComment
     * @return Response
     */
    public function addComment(Request $request, AddComment $addComment): Response{
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
     * @Route("post/{id}/like", name="post.like")
     *
     * @param Post $post
     * @return void
     */
    public function like(Post $post, PostLikeRepository $postLikeRepository, Request $request){
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message'=>'Unauthorized'], 403);
        }
        $content = $request->getContent();
        $requestdata = json_decode($content,true);
        $token = $requestdata["token"];
        $isTokenValid = $this->isCsrfTokenValid($user->getId(), $token);
        if($isTokenValid){
            if($post->isLikedByUser($this->getUser())){
                $like = $postLikeRepository->findOneBy([
                    'post'=>$post,
                    'user'=>$user,
                    ]);
                $this->em->remove($like);
                $this->em->flush();

                return $this->json([
                    'message'=>'like deleted',
                    'count'=>$post->getCountLikes(),
                ], 200);
            }

            if($post->isDislikedByUser($this->getUser())){
                $like = $postLikeRepository->findOneBy([
                    'post'=>$post,
                    'user'=>$user,
                    ]);
                $like->setValue(true);
                $this->em->persist($like);
                $this->em->flush();

                return $this->json([
                    'message'=>'dislike deleted like added',
                    'count'=>$post->getCountLikes(),
                ], 200);
            }

            $like = new PostLike();
            $like->setUser($user)->setValue(true);
            $post->addLike($like);
            $this->em->persist($post);
            $this->em->flush();


            return $this->json([
                'message'=>'like added',
                'count'=>$post->getCountLikes(),
            ], 200);
        }
        return $this->json(['message'=>'token invalid'], 403);
    }

    /**
     * @Route("post/{id}/dislike", name="post.dislike")
     *
     * @param Post $post
     * @return void
     */
    public function dislike(Post $post, PostLikeRepository $postLikeRepository, Request $request){
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message'=>'Unauthorized'], 403);
        }
        $content = $request->getContent();
        $requestdata = json_decode($content,true);
        $token = $requestdata["token"];
        $isTokenValid = $this->isCsrfTokenValid($user->getId(), $token);
        if($isTokenValid){
            if($post->isLikedByUser($this->getUser())){
                $like = $postLikeRepository->findOneBy([
                    'post'=>$post,
                    'user'=>$user,
                    ]);
                $like->setValue(false);
                $this->em->persist($like);
                $this->em->flush();

                return $this->json([
                    'message'=>'like deleted dislike added',
                    'count'=>$post->getCountLikes(),
                ], 200);
            }

            if($post->isDislikedByUser($this->getUser())){
                $like = $postLikeRepository->findOneBy([
                    'post'=>$post,
                    'user'=>$user,
                    ]);
                $this->em->remove($like);
                $this->em->flush();

                return $this->json([
                    'message'=>'dislike deleted',
                    'count'=>$post->getCountLikes(),
                ], 200);
            }

            $like = new PostLike();
            $like->setUser($user)->setValue(false);
            $post->addLike($like);
            $this->em->persist($post);
            $this->em->flush();


            return $this->json([
                'message'=>'dislike added',
                'count'=>$post->getCountLikes(),
            ], 200);
        }
        return $this->json(['message'=>'token invalid'], 403);
    }
}
