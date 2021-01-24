<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index($id, $slug, $subreddit, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
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
     * @Route("/addcomment", name="comment.add")
     *
     * @return void
     */
    public function addComment(Request $request, CommentRepository $commentRepository, PostRepository $postRepository, EntityManagerInterface $em){
        if($this->getUser() == null){
            return $this->redirectToRoute('home');
        }
        if($request->request->get('comment') == ""){
            return $this->redirectToRoute('home');
        }

        if($request->request->get('commentId')!= null){
            $comment = $commentRepository->find($request->request->get("commentId"));
            $post = $comment->getPost();
        }elseif($request->request->get('postId')!= null){
            $post = $postRepository->find($request->request->get('postId'));
        }
        $id = $post->getId();
        $slug = $post->getSlug();
        $subreddit = $post->getSubreddit()->getTitle();
    
        $newComment = new Comment();
        $newComment->setContent($request->request->get("comment"))
            ->setUser($this->getUser())
            ->setPost($post);

        if($request->request->get('commentId')!= null){
            $newComment->setCommentParent($comment);
            $comment->addComment($newComment);
             $em->persist($comment);
            $em->flush();
        }elseif($request->request->get('postId')!= null){
            $post->addComment($newComment);
            $em->persist($post);
            $em->flush();
        }

        return $this->redirectToRoute('post.show', [
            'id' => $id,
            'slug'=>$slug,
            'subreddit' => $subreddit,
        ]);
    }
}
