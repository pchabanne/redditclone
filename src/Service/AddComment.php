<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Service used when an user add a comment on a post or on a comment
 */
class AddComment{

    private $em;
    private $security;
    private $router;
    private $requestStack;
    private $commentRepository;
    private $postRepository;

    public function __construct(EntityManagerInterface $em, Security $security, UrlGeneratorInterface $router, RequestStack $requestStack, CommentRepository $commentRepository, PostRepository $postRepository)
    {
        $this->em = $em;
        $this->security = $security;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * check if the user and if the content of the comment is not null
     *
     * @param null $content
     * @return string|null
     */
    public function checkParameters($content) :?string{
        if($this->security->getUser() == null){
            return $this->router->generate('home');
        }
        if($content == ""){
            return $this->router->generate('home');
        }

        return null;
    }

    /**
     * return the post object where the comment is and redirect to the addCommentToComment function or too the AddCommentToPost function
     *
     * @param string $content
     * @return Post
     */
    public function addComment($content) :Post{
        $request = $this->requestStack->getCurrentRequest();
        if($request->request->get('commentId') != null){
            $comment = $this->commentRepository->find($request->request->get("commentId"));
            $post = $comment->getPost();
            $this->addCommentToComment($comment, $content);
            return $post;
        }elseif($request->request->get('postId') != null){
            $post = $this->postRepository->find($request->request->get('postId'));
            $this->addCommentToPost($post, $content);
            return $post;
        }
    }
    
    /**
     * add the comment to a post (the comment did not have a parent)
     *
     * @param Post $post
     * @param string $content
     * @return void
     */
    public function addCommentToPost(Post $post, $content){
        $newComment = new Comment();
        $newComment->setContent($content)
            ->setUser($this->security->getUser())
            ->setPost($post);
        $post->addComment($newComment);
        $this->em->persist($post);
        $this->em->flush();
    }

    /**
     * add the comment to a post (the comment have a parent)
     *
     * @param Comment $comment
     * @param string $content
     * @return void
     */
    public function addCommentToComment(Comment $comment, $content){
        $post = $comment->getPost();
        $newComment = new Comment();
        $newComment->setContent($content)
            ->setUser($this->security->getUser())
            ->setPost($post)
            ->setCommentParent($comment);
        $comment->addComment($newComment);
        $this->em->persist($comment);
        $this->em->flush();
            
    }

}