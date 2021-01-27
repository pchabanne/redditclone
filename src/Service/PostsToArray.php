<?php

namespace App\Service;
use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostsToArray{

    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * return a collection of posts into an array in order to send it in ajax
     *
     * @param Post[] $posts
     * @return Array
     */
    public function postsToArray($posts){
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
            if($this->user){
                $postJson['isLiked'] = $post->isLikedByUser($this->user);
                $postJson['isDisliked'] = $post->isDislikedByUser($this->user);
            }else{
                $postJson['isLiked'] = false;
                $postJson['isDisliked'] = false;
            }

            array_push($postsJson, $postJson);
        }

        return $postsJson;
    }
}