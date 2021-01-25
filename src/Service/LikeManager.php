<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Service used when a post is liked or disliked
 */
class LikeManager{
    private $em;
    private $user;
    private $postLikeRepository;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, PostLikeRepository $postLikeRepository)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->postLikeRepository = $postLikeRepository;
    }

    /**
     * handle a like on a post and return parameters of the response
     *
     * @param Post $post
     * @return array
     */
    public function handleLike(Post $post) :array{
        if($post->isLikedByUser($this->user)){
            $like = $this->postLikeRepository->findOneBy([
                'post'=>$post,
                'user'=>$this->user,
                ]);
            $this->em->remove($like);
            $this->em->flush();
            $response = [
                'message'=>'like deleted',
                'count'=>$post->getCountLikes(),
            ];
            return $response;
        }

        if($post->isDislikedByUser($this->user)){
            $like = $this->postLikeRepository->findOneBy([
                'post'=>$post,
                'user'=>$this->user,
                ]);
            $like->setValue(true);
            $this->em->persist($like);
            $this->em->flush();
            $response = [
                'message'=>'dislike deleted like added',
                'count'=>$post->getCountLikes(),
            ];
            return $response;
        }

        $like = new PostLike();
        $like->setUser($this->user)->setValue(true);
        $post->addLike($like);
        $this->em->persist($post);
        $this->em->flush();
        $response = [
            'message'=>'like added',
            'count'=>$post->getCountLikes(),
        ];
        return $response;
    }

    /**
     * handle a dislike on a post and return parameters of the response
     *
     * @param Post $post
     * @return array
     */
    public function handleDislike(Post $post) :array{
        if($post->isLikedByUser($this->user)){
            $like = $this->postLikeRepository->findOneBy(['post'=>$post, 'user'=>$this->user]);
            $like->setValue(false);
            $this->em->persist($like);
            $this->em->flush();
            $response = [
                'message'=>'like deleted dislike added',
                'count'=>$post->getCountLikes(),
            ];

            return $response;
        }

        if($post->isDislikedByUser($this->user)){
            $like = $this->postLikeRepository->findOneBy([
                'post'=>$post,
                'user'=>$this->user,
                ]);
            $this->em->remove($like);
            $this->em->flush();
            $response = [
                'message'=>'dislike deleted',
                'count'=>$post->getCountLikes(),
            ];
            return $response;
        }

        $like = new PostLike();
        $like->setUser($this->user)->setValue(false);
        $post->addLike($like);
        $this->em->persist($post);
        $this->em->flush();
        $response = [
            'message'=>'dislike added',
            'count'=>$post->getCountLikes(),
        ];


        return $response;
    }
}