<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AddComment{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }
    
    public function addCommentToPost(Post $post, $content){
        $newComment = new Comment();
        $newComment->setContent($content)
            ->setUser($this->security->getUser())
            ->setPost($post);
        $post->addComment($newComment);
        $this->em->persist($post);
        $this->em->flush();
    }

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