<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * return the home page
     * @Route("/", name="home")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAllOrderByDateAjax(0,8);
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    /**
     * @Route("/get/posts", name="ajax_home_post")
     *
     * @param PostRepository $postRepository
     * @param Request $request
     * @return void
     */
    public function ajax(PostRepository $postRepository, Request $request){
        $first = $request->query->get('first');
        $limit = $request->query->get('limit');
        $posts = $postRepository->findAllOrderByDateAjax($first, $limit);
        
        
        $postsJson = [];
        $i = 0;
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
}
