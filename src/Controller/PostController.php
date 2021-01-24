<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\AddComment;
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
     * add a comment on a post or on a comment
     * @Route("/addcomment", name="comment.add")
     *
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @param PostRepository $postRepository
     * @param AddComment $addComment
     * @return Response
     */
    public function addComment(Request $request, CommentRepository $commentRepository, PostRepository $postRepository, AddComment $addComment): Response{
        if($this->getUser() == null){
            return $this->redirectToRoute('home');
        }
        if($request->request->get('comment') == ""){
            return $this->redirectToRoute('home');
        }

        if($request->request->get('commentId') != null){
            $comment = $commentRepository->find($request->request->get("commentId"));
            $post = $comment->getPost();
            $addComment->addCommentToComment($comment, $request->request->get('comment'));
        }elseif($request->request->get('postId') != null){
            $post = $postRepository->find($request->request->get('postId'));
            $addComment->addCommentToPost($post, $request->request->get('comment'));
        }

        $id = $post->getId();
        $slug = $post->getSlug();
        $subreddit = $post->getSubreddit()->getTitle();

        return $this->redirectToRoute('post.show', [
            'id' => $id,
            'slug'=>$slug,
            'subreddit' => $subreddit,
        ]);
    }
}
